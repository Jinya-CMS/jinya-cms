<?php

namespace App\Routing;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\LoadableEntity;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use App\Web\Attributes\Authenticated;
use App\Web\Exceptions\MissingFieldsException;
use DateInterval;
use DateTime;
use Iterator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionClass;
use ReflectionProperty;
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
        if (!$reflectionClass->isSubclassOf(LoadableEntity::class) || !$reflectionClass->implementsInterface(FormattableEntityInterface::class)) {
            throw new HttpNotFoundException($request);
        }

        $defaultAttributeValues = [
            'createEnabled' => true,
            'createRole' => Authenticated::WRITER,
            'readEnabled' => true,
            'readRole' => Authenticated::READER,
            'updateEnabled' => true,
            'updateRole' => Authenticated::WRITER,
            'deleteEnabled' => true,
            'deleteRole' => Authenticated::WRITER,
        ];
        $jinyaApiAttributeArguments = $jinyaApiAttributes[0]->getArguments();
        $jinyaApiArguments = [...$defaultAttributeValues, ...$jinyaApiAttributeArguments];
        $method = strtolower($request->getMethod());

        if ($method === 'get' && $jinyaApiArguments['readEnabled']) {
            return self::executeGetRequest($reflectionClass, $jinyaApiArguments['readRole'], $request, $response, $args);
        }
        if ($method === 'delete' && $jinyaApiArguments['deleteEnabled']) {
            return self::executeDeleteRequest($reflectionClass, $jinyaApiArguments['deleteRole'], $request, $response, $args);
        }
        if ($method === 'post' && $jinyaApiArguments['createEnabled']) {
            return self::executePostRequest($reflectionClass, $jinyaApiArguments['createRole'], $request, $response);
        }
        if ($method === 'put' && $jinyaApiArguments['updateEnabled']) {
            return self::executePutRequest($reflectionClass, $jinyaApiArguments['updateRole'], $request, $response, $args);
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

    private static function executeDeleteRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response, array $args): Response
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

    private static function executePostRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response): Response
    {
        self::checkRole(self::getCurrentUser($request), $request, $role);
        $entity = $reflectionClass->newInstance();
        $requestBody = $request->getBody()->getContents();
        $body = json_decode($requestBody, true) ?? [];
        $requiredFieldMissing = false;
        $missingRequiredFields = [];
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $apiFieldAttributes = $reflectionProperty->getAttributes(JinyaApiField::class);
            foreach ($apiFieldAttributes as $apiFieldAttribute) {
                $apiFieldArguments = $apiFieldAttribute->getArguments();
                $ignore = $apiFieldArguments['ignore'] ?? false;
                if ($ignore) {
                    continue;
                }

                $required = $apiFieldArguments['required'] ?? false;
                $fieldName = $reflectionProperty->getName();
                if ($required && !array_key_exists($fieldName, $body)) {
                    $missingRequiredFields[] = $fieldName;
                    $requiredFieldMissing = true;
                    continue;
                }

                $entity->{$fieldName} = $body[$fieldName];
            }
        }

        if ($requiredFieldMissing) {
            throw new MissingFieldsException($request, $missingRequiredFields);
        }

        if (method_exists($entity, 'create')) {
            $entity->create();
            if (method_exists($entity, 'format')) {
                $response->getBody()->write(json_encode($entity->format(), JSON_THROW_ON_ERROR));
                return $response->withAddedHeader('Content-Type', 'application/json')->withStatus(201);
            }

            return $response->withStatus(204);
        }

        throw new HttpMethodNotAllowedException($request);
    }

    private static function executePutRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response, array $args): Response
    {
        self::checkRole(self::getCurrentUser($request), $request, $role);
        if (array_key_exists('id', $args)) {
            $id = $args['id'];
            if ($reflectionClass->hasMethod('findById')) {
                $method = $reflectionClass->getMethod('findById');
                $entity = $method->invoke(null, $id);
                $requestBody = $request->getBody()->getContents();
                $body = json_decode($requestBody, true) ?? [];
                foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
                    $apiFieldAttributes = $reflectionProperty->getAttributes(JinyaApiField::class);
                    foreach ($apiFieldAttributes as $apiFieldAttribute) {
                        $apiFieldArguments = $apiFieldAttribute->getArguments();
                        $ignore = $apiFieldArguments['ignore'] ?? false;
                        if ($ignore) {
                            continue;
                        }

                        $fieldName = $reflectionProperty->getName();
                        if (!array_key_exists($fieldName, $body)) {
                            continue;
                        }

                        $entity->{$fieldName} = $body[$fieldName];
                    }
                }

                if (method_exists($entity, 'update')) {
                    $entity->update();

                    return $response->withStatus(204);
                }
            }
        } else {
            throw new HttpNotFoundException($request);
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
        CurrentUser::$currentUser = $artist;

        return $apiKey->getArtist();
    }

    /**
     * @param Request $request
     * @param ReflectionClass $reflectionClass
     * @param mixed $entity
     * @return mixed
     */
    private static function setEntityValues(Request $request, ReflectionClass $reflectionClass, mixed $entity): mixed
    {

        return $entity;
    }
}