<?php

use App\Web\Actions\LocateIp\LocatorAction;
use App\Web\Middleware\AuthenticationMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->get('ip-location/{ip}', LocatorAction::class)->add(AuthenticationMiddleware::class);
};