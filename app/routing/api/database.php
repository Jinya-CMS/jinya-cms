<?php

use App\Web\Actions\Database\DatabaseAnalyzerAction;
use App\Web\Actions\Database\ExecuteQueryAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group(
        'maintenance/database',
        function (RouteCollectorProxy $group) {
            $group->post('/query', ExecuteQueryAction::class)->add(new CheckRequiredFieldsMiddleware(['query']));
            $group->get('/analyze', DatabaseAnalyzerAction::class);
        }
    )->add(new RoleMiddleware(RoleMiddleware::ROLE_SUPER_ADMIN))->add(AuthenticationMiddleware::class);
};
