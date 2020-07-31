<?php

use App\Web\Actions\Environment\GetEnvironmentAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api
        ->get('environment', GetEnvironmentAction::class)
        ->add(new RoleMiddleware(RoleMiddleware::ROLE_SUPER_ADMIN))
        ->add(AuthenticationMiddleware::class);
};