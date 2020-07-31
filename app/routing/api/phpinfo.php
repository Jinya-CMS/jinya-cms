<?php

use App\Web\Actions\PhpInfo\GetPhpInfoAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api
        ->get('phpinfo', GetPhpInfoAction::class)
        ->add(new RoleMiddleware(RoleMiddleware::ROLE_ADMIN))
        ->add(AuthenticationMiddleware::class);
};