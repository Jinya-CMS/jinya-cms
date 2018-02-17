<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:40
 */

namespace Jinya\Framework;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NoResultException;
use Jinya\Exceptions\InvalidContentTypeException;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Services\Base\BaseArtServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Throwable;
use function array_key_exists;
use function json_decode;
use function property_exists;
use function simplexml_load_string;

abstract class BaseApiController extends AbstractController
{
    /** @var TranslatorInterface */
    private $translator;
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
     * @param RequestStack $requestStack
     */
    public function __construct(TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->request = $requestStack->getCurrentRequest();
        $this->contentType = $this->request->headers->get('Content-Type');
        if ($this->contentType === 'application/json') {
            $this->bodyAsJson = json_decode($this->request->getContent(), true);
        }
        if ($this->contentType === 'text/xml') {
            $this->bodyAsXml = simplexml_load_string($this->request->getContent());
        }
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null|SimpleXMLElement
     * @throws InvalidContentTypeException
     */
    protected function getValue(string $key, $default = null)
    {
        switch ($this->contentType) {
            case 'application/json':
                if (array_key_exists($key, $this->bodyAsJson)) {
                    return $this->bodyAsJson[$key];
                } else {
                    return $default;
                }
                break;
            case 'text/xml':
                if (property_exists($this->bodyAsXml, $key)) {
                    return $this->bodyAsXml->${$key};
                } else {
                    return $default;
                }
                break;
            case 'x-www-form-urlencoded':
                return $this->request->get($key, $default);
                break;
            default:
                throw new InvalidContentTypeException($this->contentType, $this->translator->trans('api.generic.headers.contenttype', ['contentType' => $this->contentType], 'validators'));
        }
    }

    /**
     * Gets all arts from the given service
     *
     * @param Request $request
     * @param BaseArtServiceInterface $baseService
     * @param RouterInterface $router
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    protected function getAllArt(Request $request, BaseArtServiceInterface $baseService, RouterInterface $router, LabelServiceInterface $labelService): Response
    {
        list($data, $statusCode) = $this->tryExecute(function () use ($labelService, $router, $request, $baseService) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');
            $label = $request->get('label', null);

            if ($label) {
                $label = $labelService->getLabel($label);
            }

            $entityCount = $baseService->countAll($keyword);
            $entities = $baseService->getAll($offset, $count, $keyword, $label);

            $route = $request->get('_route');
            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword];

            if ($entityCount > $offset + $count) {
                $parameter['offset'] = $offset + $count;
                $next = $router->generate($route, $parameter);
            } else {
                $next = false;
            }

            if ($offset > 0) {
                $parameter['offset'] = $offset - $count;
                $previous = $router->generate($route, $parameter);
            } else {
                $previous = false;
            }

            return [
                'success' => true,
                'offset' => $offset,
                'count' => $entityCount,
                'items' => $entities,
                'control' => [
                    'next' => $next,
                    'previous' => $previous
                ]
            ];
        });

        return $this->json($data, $statusCode);
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
            return [$function(), $successStatusCode];
        } catch (MissingFieldsException $exception) {
            $data = [
                'success' => false,
                'validation' => []
            ];

            foreach ($exception->getFields() as $key => $message) {
                $data['validation'][$key] = $this->translator->trans($message, [], 'validators');
            }

            return [$data, Response::HTTP_BAD_REQUEST];
        } catch (EntityNotFoundException|FileNotFoundException|NoResultException $exception) {
            return [$this->jsonFormatException($this->translator->trans('api.state.404.generic'), $exception), Response::HTTP_NOT_FOUND];
        } catch (UniqueConstraintViolationException $exception) {
            return [$this->jsonFormatException($this->translator->trans('api.state.409.exists'), $exception), Response::HTTP_CONFLICT];
        } catch (Throwable $throwable) {
            return [$this->jsonFormatException($this->translator->trans('api.state.500.generic'), $throwable), Response::HTTP_INTERNAL_SERVER_ERROR];
        }
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
                'message' => $message
            ]
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
     * Checks if we are currently in a debugging environment
     *
     * @return bool
     */
    private function isDebug(): bool
    {
        $env = $_SERVER['APP_ENV'] ?? 'dev';
        return $_SERVER['APP_DEBUG'] ?? ('prod' !== $env);
    }

    /**
     * Gets the art for the given slug
     *
     * @param string $slug
     * @param BaseArtServiceInterface $baseService
     * @return Response
     */
    protected function getArt(string $slug, BaseArtServiceInterface $baseService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $baseService) {
            return [
                'success' => true,
                'item' => $baseService->get($slug)
            ];
        });

        return $this->json($data, $status);
    }
}