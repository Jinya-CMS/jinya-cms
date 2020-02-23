<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:40
 */

namespace Jinya\Framework;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NoResultException;
use Jinya\Exceptions\EmptyBodyException;
use Jinya\Exceptions\InvalidContentTypeException;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Framework\Security\UnknownDeviceException;
use Jinya\Services\Twig\CompilerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use SimpleXMLElement;
use Symfony\Component\Finder\Exception\AccessDeniedException as FinderAccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException as HttpAccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as CoreAccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use function array_key_exists;
use function json_decode;
use function property_exists;
use function simplexml_load_string;

abstract class BaseApiController extends BaseController
{
    /** @var TranslatorInterface */
    private TranslatorInterface $translator;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var UrlGeneratorInterface */
    private UrlGeneratorInterface $urlGenerator;

    /** @var Request */
    private ?Request $request;

    /** @var array */
    private $bodyAsJson;

    /** @var SimpleXMLElement */
    private SimpleXMLElement $bodyAsXml;

    /** @var string */
    private $contentType;

    /**
     * BaseApiController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     * @param HttpKernelInterface $kernel
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     * @param CompilerInterface $compiler
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        HttpKernelInterface $kernel,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        CompilerInterface $compiler
    ) {
        parent::__construct($requestStack, $kernel, $authorizationChecker, $tokenStorage, $router, $compiler);
        $this->translator = $translator;
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;

        $this->request = $requestStack->getCurrentRequest();
        $this->contentType = $this->request->headers->get('Content-Type');
        if ('application/json' === $this->contentType) {
            $this->bodyAsJson = json_decode($this->request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        }
        if ('text/xml' === $this->contentType) {
            $this->bodyAsXml = simplexml_load_string($this->request->getContent());
        }
    }

    /**
     * Generates a url from the given route
     *
     * @param string $route
     * @param array $parameter
     * @param bool $fullUrl
     * @return string
     */
    protected function generateUrl(string $route, array $parameter = [], bool $fullUrl = false): string
    {
        return $this->urlGenerator->generate(
            $route,
            $parameter,
            $fullUrl ? UrlGeneratorInterface::ABSOLUTE_PATH : UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     * @throws InvalidContentTypeException
     * @throws EmptyBodyException
     */
    protected function getValue(string $key, $default = null)
    {
        switch ($this->contentType) {
            case 'application/json':
                if (empty($this->bodyAsJson)) {
                    throw new EmptyBodyException('api.generic.body.empty');
                }

                if (array_key_exists($key, $this->bodyAsJson)) {
                    $result = $this->bodyAsJson[$key];
                } else {
                    $result = $default;
                }

                break;
            case 'text/xml':
                if (empty($this->bodyAsXml)) {
                    throw new EmptyBodyException('api.generic.body.empty');
                }

                if (property_exists($this->bodyAsXml, $key)) {
                    $result = $this->bodyAsXml->${$key};
                } else {
                    $result = $default;
                }

                break;
            case 'x-www-form-urlencoded':
                $result = $this->request->get($key, $default);

                break;
            default:
                throw new InvalidContentTypeException(
                    $this->contentType ?? '',
                    $this->translator->trans(
                        'api.generic.headers.contenttype',
                        [
                            'contentType' => $this->contentType
                        ],
                        'validators'
                    )
                );
        }

        return $result;
    }

    /**
     * Gets the given header
     *
     * @param string $header
     * @param string $defaultValue
     * @return string
     */
    protected function getHeader(string $header, string $defaultValue): string
    {
        return $this->request->headers->get($header, $defaultValue);
    }

    /**
     * Executes the given @param callable $function
     * @param int $successStatusCode
     * @return array
     * @see callable and return a formatted error if it fails
     */
    protected function tryExecute(callable $function, int $successStatusCode = Response::HTTP_OK): array
    {
        try {
            $result = [$function(), $successStatusCode];
        } catch (MissingFieldsException $exception) {
            $data = [
                'success' => false,
                'validation' => [],
            ];

            foreach ($exception->getFields() as $key => $message) {
                $data['validation'][$key] = $this->translator->trans($message, [], 'validators');
            }

            $result = [$data, Response::HTTP_BAD_REQUEST];
        } catch (HttpAccessDeniedException |
        CoreAccessDeniedException |
        FinderAccessDeniedException |
        BadCredentialsException $exception) {
            $this->logException($exception, 403);

            $result = [$this->jsonFormatException('api.state.403.generic', $exception), Response::HTTP_FORBIDDEN];
        } catch (EntityNotFoundException | FileNotFoundException | NoResultException $exception) {
            $this->logException($exception, 404);

            $result = [$this->jsonFormatException('api.state.404.generic', $exception), Response::HTTP_NOT_FOUND];
        } catch (EmptyBodyException $exception) {
            $this->logException($exception, 400);

            $result = [$this->jsonFormatException($exception->getMessage(), $exception), Response::HTTP_BAD_REQUEST];
        } catch (UniqueConstraintViolationException $exception) {
            $this->logException($exception, 409);

            $result = [$this->jsonFormatException('api.state.409.exists', $exception), Response::HTTP_CONFLICT];
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->logException($exception, 409);

            $result = [
                $this->jsonFormatException('api.state.409.foreign_key_failed', $exception),
                Response::HTTP_CONFLICT,
            ];
        } catch (NotNullConstraintViolationException $exception) {
            $this->logException($exception, 409);

            $result = [
                $this->jsonFormatException('api.state.409.not_null_failed', $exception),
                Response::HTTP_CONFLICT,
            ];
        } catch (UnknownDeviceException $exception) {
            $this->logException($exception, 401);

            $result = [
                $this->jsonFormatException('api.state.401.unknown_device', $exception),
                Response::HTTP_UNAUTHORIZED,
            ];
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            $this->logger->error($throwable->getTraceAsString());

            $result = [
                $this->jsonFormatException('api.state.500.generic', $throwable),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }

        return $result;
    }

    /**
     * @param Throwable $exception
     * @param $code
     */
    protected function logException(Throwable $exception, $code): void
    {
        $this->logger->warning("$code thrown – " . gettype($exception));
        $this->logger->warning($exception->getLine() . ': ' . $exception->getFile());
        if ($this->isDebug()) {
            $this->logger->warning($exception->getMessage());
            $this->logger->warning($exception->getTraceAsString());
        }
    }

    /**
     * Checks if we are currently in a debugging environment
     *
     * @return bool
     */
    private function isDebug(): bool
    {
        return 'yes' === getenv('APP_DEBUG');
    }

    /**
     * Formats the given @param string $message
     * @param Throwable $throwable
     * @return array
     * @see Throwable as array
     */
    protected function jsonFormatException(string $message, Throwable $throwable): array
    {
        try {
            $type = (new ReflectionClass($throwable))->getShortName();
        } catch (ReflectionException $e) {
            $type = 'unknown';
        }
        $data = [
            'success' => false,
            'error' => [
                'message' => $message,
                'type' => $type,
            ],
        ];
        if ($this->isDebug()) {
            $data['error']['exception'] = $throwable->getMessage();
            $data['error']['file'] = $throwable->getFile();
            $data['error']['stack'] = $throwable->getTraceAsString();
            $data['error']['line'] = $throwable->getLine();
        }

        return $data;
    }

    /**
     * @param int $totalCount
     * @param int $offset
     * @param int $count
     * @param array $parameter
     * @param string $route
     * @param array $entities
     * @return array
     */
    protected function formatListResult(
        int $totalCount,
        int $offset,
        int $count,
        array $parameter,
        string $route,
        array $entities
    ): array {
        if ($totalCount > $offset + $count) {
            $parameter['offset'] = $offset + $count;
            $next = $this->urlGenerator->generate($route, $parameter);
        } else {
            $next = false;
        }

        if ($offset > 0) {
            $parameter['offset'] = $offset - $count;
            $previous = $this->urlGenerator->generate($route, $parameter);
        } else {
            $previous = false;
        }

        return [
            'success' => true,
            'offset' => $offset,
            'count' => $totalCount,
            'items' => $entities,
            'control' => [
                'next' => $next,
                'previous' => $previous,
            ],
        ];
    }
}
