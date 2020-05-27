<?php

use App\Web\Actions\Gallery\CreateGalleryAction;
use App\Web\Actions\Gallery\DeleteGalleryAction;
use App\Web\Actions\Gallery\GetGalleryBySlugAction;
use App\Web\Actions\Gallery\ListAllGalleriesAction;
use App\Web\Actions\Gallery\Positions\GetPositionsAction;
use App\Web\Actions\Gallery\UpdateGalleryAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('media/gallery', function (RouteCollectorProxy $group) {
        $group->get('', ListAllGalleriesAction::class);
        $group->post('', CreateGalleryAction::class)->add(new CheckRequiredFieldsMiddleware(['name']));
        $group->get('/{slug}', GetGalleryBySlugAction::class);
        $group->put('/{slug}', UpdateGalleryAction::class);
        $group->delete('/{slug}', DeleteGalleryAction::class);
        $group->group('/file/{galleryId}/file', function (RouteCollectorProxy $file) {
            $file->get('', GetPositionsAction::class);
        });
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};