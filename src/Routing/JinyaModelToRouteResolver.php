<?php

namespace App\Routing;

use App\Authentication\AuthenticationChecker;
use App\Authentication\CurrentUser;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\LoadableEntity;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use App\Web\Exceptions\MissingFieldsException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;

/**
 * This class checks the database namespace and generates the CRUD endpoints for the Jinya api. The endpoints are auto generated based on the JinyaApi attribute
 * @see JinyaApi
 */
class JinyaModelToRouteResolver
{
    /**
     * Creates a response based on the passed class and id
     *
     * @param Request $request The request to handle
     * @param Response $response The response to extend and return
     * @param array<string, string> $args Contains an entity field, and in some cases an ID
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws JsonException
     * @throws NoResultException
     * @throws ReflectionException
     * @throws UniqueFailedException
     */
    public static function resolveActionWithClassAndId(Request $request, Response $response, array $args): Response
    {
        $class = '';

        $classParts = explode('-', $args['entity']);
        foreach ($classParts as $classPart) {
            $class .= ucfirst($classPart);
        }

        $entityClass = __JINYA_MODEL_NAMESPACE . $class;
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
            'createRole' => AuthenticationChecker::ROLE_WRITER,
            'readEnabled' => true,
            'readRole' => AuthenticationChecker::ROLE_READER,
            'updateEnabled' => true,
            'updateRole' => AuthenticationChecker::ROLE_WRITER,
            'deleteEnabled' => true,
            'deleteRole' => AuthenticationChecker::ROLE_WRITER,
        ];
        $jinyaApiAttributeArguments = $jinyaApiAttributes[0]->getArguments();
        /** @var array<string, mixed> $jinyaApiArguments */
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

    /**
     * Executes a GET request
     *
     * @param ReflectionClass<LoadableEntity> $reflectionClass The reflection class that reflects the entity
     * @param string $role The role needed to execute the request
     * @param Request $request The request to handle
     * @param Response $response The response to base the returned response on
     * @param array<string, string> $args If this array contains the key id, the entity with the given id is fetched. Otherwise, all entities are returned or filtered by the keyword query parameter
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws JsonException
     * @throws NoResultException
     * @throws ReflectionException
     * @throws UniqueFailedException
     */
    private static function executeGetRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response, array $args): Response
    {
        self::checkRole($request, $role);
        if (array_key_exists('id', $args)) {
            $id = $args['id'];
            if ($reflectionClass->hasMethod('findById')) {
                $method = $reflectionClass->getMethod('findById');
                /** @var FormattableEntityInterface $entity */
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
                $data = [];
                foreach ($result as $item) {
                    $data[] = $item->format();
                }
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

    /**
     * Executes a DELETE request
     *
     * @param ReflectionClass<LoadableEntity> $reflectionClass The reflection class that reflects the entity
     * @param string $role The role needed to execute the request
     * @param Request $request The request to handle
     * @param Response $response The response to base the returned response on
     * @param array<string, string> $args Must contain the key id
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws ReflectionException
     * @throws UniqueFailedException
     */
    private static function executeDeleteRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response, array $args): Response
    {
        self::checkRole($request, $role);
        if (array_key_exists('id', $args)) {
            $id = $args['id'];
            if ($reflectionClass->hasMethod('findById')) {
                $method = $reflectionClass->getMethod('findById');
                /** @var LoadableEntity $entity */
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

    /**
     * Executes a POST request
     *
     * @param ReflectionClass<LoadableEntity> $reflectionClass The reflection class that reflects the entity
     * @param string $role The role needed to execute the request
     * @param Request $request The request to handle
     * @param Response $response The response to base the returned response on
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws JsonException
     * @throws NoResultException
     * @throws ReflectionException
     * @throws UniqueFailedException
     */
    private static function executePostRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response): Response
    {
        self::checkRole($request, $role);
        /** @var LoadableEntity $entity */
        $entity = $reflectionClass->newInstance();
        $requestBody = $request->getBody()->getContents();
        /** @noinspection JsonEncodingApiUsageInspection */
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
                if (array_key_exists($fieldName, $body)) {
                    $entity->{$fieldName} = $body[$fieldName];
                }
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

    /**
     * @param ReflectionClass<LoadableEntity> $reflectionClass The reflection class that reflects the entity
     * @param string $role The role needed to execute the request
     * @param Request $request The request to handle
     * @param Response $response The response to base the returned response on
     * @param array<string, string> $args This array must contain a key id, otherwise an HttpNotFoundException is thrown
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws ReflectionException
     * @throws UniqueFailedException
     */
    private static function executePutRequest(ReflectionClass $reflectionClass, string $role, Request $request, Response $response, array $args): Response
    {
        self::checkRole($request, $role);
        if (array_key_exists('id', $args)) {
            $id = $args['id'];
            if ($reflectionClass->hasMethod('findById')) {
                $method = $reflectionClass->getMethod('findById');
                /** @var LoadableEntity $entity */
                $entity = $method->invoke(null, $id);
                $requestBody = $request->getBody()->getContents();
                /** @noinspection JsonEncodingApiUsageInspection */
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

    /**
     * Checks whether the user in the current request has the given role and sets the current user global
     *
     * @param Request $request The request to check in
     * @param string $role The role to check for
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    private static function checkRole(Request $request, string $role): void
    {
        $artist = AuthenticationChecker::checkRequestForUser($request, $role);
        CurrentUser::$currentUser = $artist;
    }
}
