<?php

use App\Web\Actions\KnownDevice\DeleteKnownDeviceAction;
use App\Web\Actions\KnownDevice\ListAllKnownDevicesAction;
use App\Web\Middleware\AuthenticationMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('account/known_device', function (RouteCollectorProxy $group) {
        $group->get('', ListAllKnownDevicesAction::class)->add(AuthenticationMiddleware::class);
        $group->delete('/{key}', DeleteKnownDeviceAction::class)->add(AuthenticationMiddleware::class);
    });
    $api->group('known_device', function (RouteCollectorProxy $group) {
        $group->get('', ListAllKnownDevicesAction::class)->add(AuthenticationMiddleware::class);
        $group->delete('/{key}', DeleteKnownDeviceAction::class)->add(AuthenticationMiddleware::class);
    });
};