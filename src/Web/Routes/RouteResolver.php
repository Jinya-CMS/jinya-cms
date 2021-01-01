<?php

namespace App\Web\Routes;

use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Attributes\RequireOneField;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Composer\Autoload\ClassLoader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionAttribute;
use ReflectionClass;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class RouteResolver
{
    public function resolveRoutes(App $app, ClassLoader $autoLoader): RouteCollectorProxy
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../Actions'));
        $classesWithFullPath = [];
        foreach ($files as $file) {
            if (is_file($file)) {
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
                $routes[] = [
                    'jinyaAction' => $reflectionClass->getAttributes(JinyaAction::class),
                    'requiredFields' => $reflectionClass->getAttributes(RequiredFields::class),
                    'authenticated' => $reflectionClass->getAttributes(Authenticated::class),
                    'requireOneField' => $reflectionClass->getAttributes(RequireOneField::class),
                    'class' => $class,
                ];
            }
        }

        $proxy = new RouteCollectorProxy(
            $app->getResponseFactory(),
            $app->getCallableResolver(),
            $app->getContainer(),
            $app->getRouteCollector()
        );

        foreach ($routes as $route) {
            /** @var ReflectionAttribute[] $actionAttributes */
            $actionAttributes = $route['jinyaAction'];
            /** @var ReflectionAttribute[] $requiredFieldsAttributes */
            $requiredFieldsAttributes = $route['requiredFields'];
            /** @var ReflectionAttribute[] $requireOneFieldAttributes */
            $requireOneFieldAttributes = $route['requireOneField'];
            /** @var ReflectionAttribute[] $authenticatedAttributes */
            $authenticatedAttributes = $route['authenticated'];
            if (!empty($actionAttributes)) {
                $action = $actionAttributes[0];
                $actionAttribute = $action->newInstance();
                $actionRoute = $proxy->map([$actionAttribute->method], $actionAttribute->url, $route['class']);
                $actionRoute->setName($actionAttribute->name);
                if (!empty($authenticatedAttributes)) {
                    /** @var Authenticated $authenticated */
                    $authenticated = $authenticatedAttributes[0]->newInstance();
                    $actionRoute->addMiddleware(new AuthenticationMiddleware());
                    if ($authenticated->role !== '') {
                        $actionRoute->addMiddleware(new RoleMiddleware($authenticated->role));
                    }
                }

                if (!empty($requiredFieldsAttributes)) {
                    /** @var RequiredFields $attribute */
                    $attribute = $requiredFieldsAttributes[0]->newInstance();
                    $actionRoute->addMiddleware(new CheckRequiredFieldsMiddleware($attribute->requiredFields));
                }

                if (!empty($requireOneFieldAttributes)) {
                    /** @var RequireOneField $attribute */
                    $attribute = $requiredFieldsAttributes[0]->newInstance();
                    $actionRoute->addMiddleware(new CheckRequiredFieldsMiddleware($attribute->validFields));
                }
            }
        }

        return $proxy;
    }
}