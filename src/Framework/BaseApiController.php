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
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Translation\TranslatorInterface;
use Throwable;
use function array_key_exists;
use function json_decode;
use function property_exists;
use function simplexml_load_string;

abstract class BaseApiController extends BaseController
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var LabelServiceInterface */
    private $labelService;

    /** @var LoggerInterface */
    private $logger;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Request */
    private $request;

    /** @var array */
    private $bodyAsJson;

    /** @var SimpleXMLElement */
    private $bodyAsXml;

    /** @var string */
    private $contentType;

    /**
     * BaseApiController constructor.
     * @param TranslatorInterface $translator
     * @param LabelServiceInterface $labelService
     * @param LoggerInterface $logger
     * @param UrlGeneratorInterface $urlGenerator
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param RequestStack $requestStack
     * @param HttpKernelInterface $kernel
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     */
    public function __construct(TranslatorInterface $translator, LabelServiceInterface $labelService, LoggerInterface $logger, UrlGeneratorInterface $urlGenerator, ThemeConfigServiceInterface $themeConfigService, ThemeServiceInterface $themeService, ConfigurationServiceInterface $configurationService, ThemeCompilerServiceInterface $themeCompilerService, RequestStack $requestStack, HttpKernelInterface $kernel, AuthorizationCheckerInterface $authorizationChecker, \Twig_Environment $twig, TokenStorageInterface $tokenStorage, RouterInterface $router)
    {
        parent::__construct($themeConfigService, $themeService, $configurationService, $themeCompilerService, $requestStack, $kernel, $authorizationChecker, $twig, $tokenStorage, $router);
        $this->translator = $translator;
        $this->labelService = $labelService;
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;

        $this->request = $requestStack->getCurrentRequest();
        $this->contentType = $this->request->headers->get('Content-Type');
        if ('application/json' === $this->contentType) {
            $this->bodyAsJson = json_decode($this->request->getContent(), true);
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
        return $this->urlGenerator->generate($route, $parameter, $fullUrl ? UrlGeneratorInterface::ABSOLUTE_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null|SimpleXMLElement|array
     * @throws InvalidContentTypeException
     * @throws EmptyBodyException
     */
    protected function getValue(string $key, $default = null)
    {
        switch ($this->contentType) {
            case 'application/json':
                if (empty($this->bodyAsJson)) {
                    throw new EmptyBodyException('api.generic.body.empty');
                } elseif (array_key_exists($key, $this->bodyAsJson)) {
                    $result = $this->bodyAsJson[$key];
                } else {
                    $result = $default;
                }

                break;
            case 'text/xml':
                if (empty($this->bodyAsXml)) {
                    throw new EmptyBodyException('api.generic.body.empty');
                } elseif (property_exists($this->bodyAsXml, $key)) {
                    $result = $this->bodyAsXml->${$key};
                } else {
                    $result = $default;
                }

                break;
            case 'x-www-form-urlencoded':
                $result = $this->request->get($key, $default);

                break;
            default:
                throw new InvalidContentTypeException($this->contentType ?? '', $this->translator->trans('api.generic.headers.contenttype', ['contentType' => $this->contentType], 'validators'));
        }

        return $result;
    }

    /**
     * Executes the given @see callable and return a formatted error if it fails
     *
     * @param callable $function
     * @param int $successStatusCode
     * @return array
     */
    protected function tryExecute(callable $function, int $successStatusCode = Response::HTTP_OK)
    {
        try {
            $result = [$function(), $successStatusCode];
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (MissingFieldsException $exception) {
            $data = [
                'success' => false,
                'validation' => [],
            ];

            foreach ($exception->getFields() as $key => $message) {
                $data['validation'][$key] = $this->translator->trans($message, [], 'validators');
            }

            $result = [$data, Response::HTTP_BAD_REQUEST];
        } /* @noinspection PhpUnnecessaryFullyQualifiedNameInspection */ catch (\Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException | \Symfony\Component\Security\Core\Exception\AccessDeniedException | \Symfony\Component\Finder\Exception\AccessDeniedException | BadCredentialsException $exception) {
            $this->logException($exception, 403);

            $result = [$this->jsonFormatException('api.state.403.generic', $exception), Response::HTTP_FORBIDDEN];
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (EntityNotFoundException | FileNotFoundException | NoResultException $exception) {
            $this->logException($exception, 404);

            $result = [$this->jsonFormatException('api.state.404.generic', $exception), Response::HTTP_NOT_FOUND];
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (EmptyBodyException $exception) {
            $this->logException($exception, 400);

            $result = [$this->jsonFormatException($exception->getMessage(), $exception), Response::HTTP_BAD_REQUEST];
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (UniqueConstraintViolationException $exception) {
            $this->logException($exception, 409);

            $result = [$this->jsonFormatException('api.state.409.exists', $exception), Response::HTTP_CONFLICT];
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (ForeignKeyConstraintViolationException $exception) {
            $this->logException($exception, 409);

            $result = [$this->jsonFormatException('api.state.409.foreign_key_failed', $exception), Response::HTTP_CONFLICT];
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (NotNullConstraintViolationException $exception) {
            $this->logException($exception, 409);

            $result = [$this->jsonFormatException('api.state.409.not_null_failed', $exception), Response::HTTP_CONFLICT];
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            $this->logger->error($throwable->getTraceAsString());

            $result = [$this->jsonFormatException('api.state.500.generic', $throwable), Response::HTTP_INTERNAL_SERVER_ERROR];
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
     * Formats the given @see Throwable as array
     *
     * @param string $message
     * @param Throwable $throwable
     * @return array
     */
    protected function jsonFormatException(string $message, Throwable $throwable): array
    {
        $data = [
            'success' => false,
            'error' => [
                'message' => $message,
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
    protected function formatListResult(int $totalCount, int $offset, int $count, array $parameter, string $route, array $entities): array
    {
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
