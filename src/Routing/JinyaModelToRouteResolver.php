<?php

namespace App\Routing;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\Utils\FormattableEntityInterface;
use App\Routing\Attributes\JinyaApi;
use App\Web\Attributes\Authenticated;
use DateInterval;
use DateTime;
use Iterator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionClass;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;

class JinyaModelToRouteResolver
{
    public static function resolveActionWithClassAndId(Request $request, Response $response, array $args): Response
    {
        $class = $args['entity'];

        $entityClass = __JINYA_MODEL_NAMESPACE . ucfirst($class);
        if (!class_exists($entityClass, true)) {
            throw new HttpNotFoundException($request);
        }

        $reflectionClass = new ReflectionClass($entityClass);
        $jinyaApiAttributes = $reflectionClass->getAttributes(JinyaApi::class);
        if (count($jinyaApiAttributes) !== 1) {
            throw new HttpNotFoundException($request);
        }

        /** @var JinyaApi $jinyaApiAttribute */
        $jinyaApiAttribute = $jinyaApiAttributes[0]->newInstance();
        $method = strtolower($request->getMethod());

        if ($method === 'get' && $jinyaApiAttribute->readEnabled) {
            return self::executeGetRequest($reflectionClass, $jinyaApiAttribute->readRole, $request, $response, $args);
        }
        if ($method === 'delete' && $jinyaApiAttribute->deleteEnabled) {
            return self::executeDeleteRequest($reflectionClass, $jinyaApiAttribute->deleteRole, $request, $response, $args);
        }

        throw new HttpMethodNotAllowedException($request);
    }

    private static function executeGetRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response, array $args): Response
    {
        self::checkRole(self::getCurrentUser($request), $request, $role);
        if (array_key_exists('id', $args)) {
            $id = $args['id'];
            if ($reflectionClass->hasMethod('findById')) {
                $method = $reflectionClass->getMethod('findById');
                $entity = $method->invoke(null, $id);
                if (method_exists($entity, 'format')) {
                    $json = json_encode($entity->format(), JSON_THROW_ON_ERROR);
                    $response->getBody()->write($json);

                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
                }
            }
        } else {
            if (array_key_exists('keyword', $request->getQueryParams()) && $reflectionClass->hasMethod('findByKeyword')) {
                $method = $reflectionClass->getMethod('findByKeyword');
                $result = $method->invoke(null, $request->getQueryParams()['keyword']);
            } elseif ($reflectionClass->hasMethod('findAll')) {
                $method = $reflectionClass->getMethod('findAll');
                $result = $method->invoke(null);
            } else {
                throw new HttpNotFoundException($request);
            }

            if (is_iterable($result)) {
                if ($result instanceof Iterator) {
                    $result = iterator_to_array($result);
                }
                $data = array_map(static fn(FormattableEntityInterface $entity) => $entity->format(), $result);
                $payload = [
                    'offset' => 0,
                    'itemsCount' => count($data),
                    'totalCount' => count($data),
                    'items' => $data,
                ];
                $json = json_encode($payload, JSON_THROW_ON_ERROR);
                $response->getBody()->write($json);

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }
        }

        throw new HttpMethodNotAllowedException($request);
    }

    private static function checkRole(Artist $artist, Request $request, string $role): bool
    {
        $cascadedRole = match ($role) {
            Authenticated::READER => Authenticated::WRITER,
            default => '',
        };
        if (!(in_array($role, $artist->roles, true) || in_array($cascadedRole, $artist->roles, true))) {
            throw new HttpForbiddenException($request, 'Not enough permissions');
        }

        return true;
    }

    private static function getCurrentUser(Request $request): Artist
    {
        $apiKeyHeader = $request->getHeaderLine('JinyaApiKey');
        $apiKey = ApiKey::findByApiKey($apiKeyHeader);
        if (!$apiKey) {
            throw new HttpForbiddenException($request, 'Api key invalid');
        }

        $validSince = $apiKey->validSince;
        $expireAfterSeconds = getenv('JINYA_API_KEY_EXPIRY') ?: '86400';
        $validTimeSpan = new DateInterval("PT${expireAfterSeconds}S");

        if ($validSince->add($validTimeSpan)->getTimestamp() < time()) {
            $apiKey->delete();
            throw new HttpForbiddenException($request, 'Api key invalid');
        }

        $apiKey->validSince = new DateTime();
        $artist = $apiKey->getArtist();
        if (!$artist->enabled) {
            $apiKey->delete();
            throw new HttpForbiddenException($request, 'Api key invalid');
        }

        $apiKey->update();

        return $apiKey->getArtist();
    }

    private static function executeDeleteRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response, array $args)
    {
        self::checkRole(self::getCurrentUser($request), $request, $role);
        if (array_key_exists('id', $args)) {
            $id = $args['id'];
            if ($reflectionClass->hasMethod('findById')) {
                $method = $reflectionClass->getMethod('findById');
                $entity = $method->invoke(null, $id);
                if (method_exists($entity, 'delete')) {
                    $entity->delete();
                    return $response->withStatus(204);
                }
            }
        } else {
            throw new HttpNotFoundException($request);
        }

        throw new HttpMethodNotAllowedException($request);
    }
}