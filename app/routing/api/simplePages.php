<?php

use App\Web\Actions\SimplePage\CreateSimplePageAction;
use App\Web\Actions\SimplePage\DeleteSimplePageAction;
use App\Web\Actions\SimplePage\GetSimplePageBySlugAction;
use App\Web\Actions\SimplePage\ListAllSimplePagesAction;
use App\Web\Actions\SimplePage\UpdateSimplePageAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('page', function (RouteCollectorProxy $group) {
        $group->get('', ListAllSimplePagesAction::class);
        $group->post('', CreateSimplePageAction::class)->add(new CheckRequiredFieldsMiddleware(['title', 'content']));
        $group->get('/{slug}', GetSimplePageBySlugAction::class);
        $group->put('/{slug}', UpdateSimplePageAction::class);
        $group->delete('/{slug}', DeleteSimplePageAction::class);
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};