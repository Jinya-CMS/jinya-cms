<?php

use App\Web\Actions\SimplePage\GetSimplePageBySlugAction;
use App\Web\Actions\SimplePage\ListAllSimplePagesAction;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('page', function (RouteCollectorProxy $group) {
        $group->get('', ListAllSimplePagesAction::class);
        $group->get('/{slug}', GetSimplePageBySlugAction::class);
    });
};