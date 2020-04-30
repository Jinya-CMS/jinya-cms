<?php

use App\Web\Actions\Action;
use App\Web\Actions\Authentication\LoginAction;
use App\Web\Actions\Authentication\TwoFactorAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
        $group->map(['HEAD'], '', function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response->withStatus(Action::HTTP_NO_CONTENT);
        })->add(AuthenticationMiddleware::class);
    });
};