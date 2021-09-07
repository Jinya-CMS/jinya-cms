<?php

namespace App\Web\Routes;

use App\Utils\ClassResolver;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Attributes\RequireOneField;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\CheckRequiredOneOfFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use ReflectionAttribute;
use ReflectionClass;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class RouteResolver
{
    /**
     * @throws \ReflectionException
     */
    public function resolveRoutes(App $app): RouteCollectorProxy
    {
        $classes = ClassResolver::loadClasses(__ROOT__ . '/src/Web/Actions');
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
            /** @var ReflectionAttribute<JinyaAction>[] $actionAttributes */
            $actionAttributes = $route['jinyaAction'];
            /** @var ReflectionAttribute<RequiredFields>[] $requiredFieldsAttributes */
            $requiredFieldsAttributes = $route['requiredFields'];
            /** @var ReflectionAttribute<RequireOneField>[] $requireOneFieldAttributes */
            $requireOneFieldAttributes = $route['requireOneField'];
            /** @var ReflectionAttribute<Authenticated>[] $authenticatedAttributes */
            $authenticatedAttributes = $route['authenticated'];
            if (!empty($actionAttributes)) {
                foreach ($actionAttributes as $action) {
                    /** @var JinyaAction $actionAttribute */
                    $actionAttribute = $action->newInstance();
                    $actionRoute = match ($actionAttribute->method) {
                        JinyaAction::GET => $proxy->get($actionAttribute->url, $route['class']),
                        JinyaAction::POST => $proxy->post($actionAttribute->url, $route['class']),
                        JinyaAction::PUT => $proxy->put($actionAttribute->url, $route['class']),
                        JinyaAction::DELETE => $proxy->delete($actionAttribute->url, $route['class']),
                        JinyaAction::HEAD => $proxy->map(['HEAD'], $actionAttribute->url, $route['class']),
                        default => null,
                    };
                    $actionRoute->setName($route['class']);
                    if (!empty($authenticatedAttributes)) {
                        /** @var Authenticated $authenticated */
                        $authenticated = $authenticatedAttributes[0]->newInstance();
                        if ($authenticated->role !== '') {
                            $actionRoute->addMiddleware(new RoleMiddleware($authenticated->role));
                        }
                        $actionRoute->addMiddleware(new AuthenticationMiddleware());
                    }

                    if (!empty($requiredFieldsAttributes)) {
                        /** @var RequiredFields $requiredFieldsAttribute */
                        $requiredFieldsAttribute = $requiredFieldsAttributes[0]->newInstance();
                        $actionRoute->addMiddleware(
                            new CheckRequiredFieldsMiddleware($requiredFieldsAttribute->requiredFields)
                        );
                    }

                    if (!empty($requireOneFieldAttributes)) {
                        /** @var RequireOneField $requireOneFieldAttribute */
                        $requireOneFieldAttribute = $requireOneFieldAttributes[0]->newInstance();
                        $actionRoute->addMiddleware(
                            new CheckRequiredOneOfFieldsMiddleware($requireOneFieldAttribute->validFields)
                        );
                    }
                }
            }
        }

        return $proxy;
    }
}