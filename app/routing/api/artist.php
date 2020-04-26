<?php

use App\Web\Actions\Artist\CreateArtistAction;
use App\Web\Actions\Artist\DeleteArtistAction;
use App\Web\Actions\Artist\GetArtistByIdAction;
use App\Web\Actions\Artist\ListAllArtistsAction;
use App\Web\Actions\Artist\UpdateArtistAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api){
    $api->group('user', function (RouteCollectorProxy $group) {
        $group->get('', ListAllArtistsAction::class);
        $group->post('', CreateArtistAction::class)->add(new CheckRequiredFieldsMiddleware([
            'artistName',
            'email',
            'password',
        ]));
        $group->get('/{id}', GetArtistByIdAction::class);
        $group->put('/{id}', UpdateArtistAction::class);
        $group->delete('/{id}', DeleteArtistAction::class);
    })->add(AuthenticationMiddleware::class)->add(new RoleMiddleware(RoleMiddleware::ROLE_ADMIN));
};