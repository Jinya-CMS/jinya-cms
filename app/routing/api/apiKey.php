<?php

use App\Web\Actions\ApiKey\DeleteApiKeyAction;
use App\Web\Actions\ApiKey\ListAllApiKeysAction;
use App\Web\Middleware\AuthenticationMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('account/known_device', function (RouteCollectorProxy $group) {
        $group->get('', ListAllApiKeysAction::class)->add(AuthenticationMiddleware::class);
        $group->delete('/{key}', DeleteApiKeyAction::class)->add(AuthenticationMiddleware::class);
    });
    $api->group('known_device', function (RouteCollectorProxy $group) {
        $group->get('', ListAllApiKeysAction::class)->add(AuthenticationMiddleware::class);
        $group->delete('/{key}', DeleteApiKeyAction::class)->add(AuthenticationMiddleware::class);
    });
};