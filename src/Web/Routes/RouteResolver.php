<?php

namespace App\Web\Routes;

use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Attributes\RequireOneField;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\CheckRequiredOneOfFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class RouteResolver
{
    public function resolveRoutes(App $app): RouteCollectorProxy
    {
        $routes = $this->getRoutes();

        $proxy = new RouteCollectorProxy(
            $app->getResponseFactory(),
            $app->getCallableResolver(),
            $app->getContainer(),
            $app->getRouteCollector()
        );

        foreach ($routes as $route) {
            $requiredFieldsAttribute = $route->getRequiredFieldsAttribute();
            $requireOneFieldAttribute = $route->getRequireOneFieldAttribute();
            $authenticatedAttribute = $route->getAuthenticatedAttribute();
            foreach ($route->getActions() as $action) {
                $actionRoute = match ($action->method) {
                    JinyaAction::GET => $proxy->get($action->url, $route['class']),
                    JinyaAction::POST => $proxy->post($action->url, $route['class']),
                    JinyaAction::PUT => $proxy->put($action->url, $route['class']),
                    JinyaAction::DELETE => $proxy->delete($action->url, $route['class']),
                    JinyaAction::HEAD => $proxy->map(['HEAD'], $action->url, $route['class']),
                    default => null,
                };
                $actionRoute->setName($route->class);
                if ($authenticatedAttribute !== null) {
                    if ($authenticatedAttribute->role !== '') {
                        $actionRoute->addMiddleware(new RoleMiddleware($authenticatedAttribute->role));
                    }
                    $actionRoute->addMiddleware(new AuthenticationMiddleware());
                }

                if ($requiredFieldsAttribute !== null) {
                    $actionRoute->addMiddleware(
                        new CheckRequiredFieldsMiddleware($requiredFieldsAttribute->requiredFields)
                    );
                }

                if ($requireOneFieldAttribute !== null) {
                    $actionRoute->addMiddleware(
                        new CheckRequiredOneOfFieldsMiddleware($requireOneFieldAttribute->validFields)
                    );
                }
            }
        }

        return $proxy;
    }

    /**
     * @return Route[]
     * @throws \ReflectionException
     */
    public function getRoutes(): array
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../Actions'));
        $classesWithFullPath = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $classesWithFullPath[] = $file->getPathname();
            }
        }
        foreach ($classesWithFullPath as $path) {
            $class = 'App\Web' . str_replace('.php', '', implode('\\', explode('/', explode('..', $path)[1])));
            class_exists($class, true);
        }

        $classes = get_declared_classes();
        $routes = [];
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            $parentClass = $reflectionClass->getParentClass();
            if ($parentClass && str_ends_with($parentClass->getName(), 'Action')) {
                $requiredFields = $reflectionClass->getAttributes(RequiredFields::class);
                $requiredFieldsAttribute = empty($requiredFields) ? null : $requiredFields[0];
                $requiredOneField = $reflectionClass->getAttributes(RequireOneField::class);
                $requiredOneFieldAttribute = empty($requiredOneField) ? null : $requiredOneField[0];
                $authenticated = $reflectionClass->getAttributes(Authenticated::class);
                $authenticatedAttribute = empty($authenticated) ? null : $authenticated[0];
                $routes[] = new Route(
                    $reflectionClass->getAttributes(JinyaAction::class),
                    $requiredFieldsAttribute,
                    $requiredOneFieldAttribute,
                    $authenticatedAttribute,
                    $class,
                );
            }
        }
        return $routes;
    }
}