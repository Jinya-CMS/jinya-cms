<?php

use App\Web\Actions\Authentication\LoginAction;
use App\Web\Actions\Authentication\TwoFactorAction;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->post('2fa', TwoFactorAction::class)->add(new CheckRequiredFieldsMiddleware([
        'username',
        'password',
    ]));
    $api->group('login', function (RouteCollectorProxy $group) {
        $group->post('', LoginAction::class)->add(new CheckRequiredFieldsMiddleware([
            'username',
            'password',
        ]));
    });
};