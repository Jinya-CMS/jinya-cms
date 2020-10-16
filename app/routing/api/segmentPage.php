<?php

use App\Web\Actions\SegmentPage\CreateSegmentPageAction;
use App\Web\Actions\SegmentPage\DeleteSegmentPageAction;
use App\Web\Actions\SegmentPage\GetSegmentPageByIdAction;
use App\Web\Actions\SegmentPage\ListAllSegmentPagesAction;
use App\Web\Actions\SegmentPage\Segments\CreateFileSegmentAction;
use App\Web\Actions\SegmentPage\Segments\CreateFormSegmentAction;
use App\Web\Actions\SegmentPage\Segments\CreateGallerySegmentAction;
use App\Web\Actions\SegmentPage\Segments\CreateHtmlSegmentAction;
use App\Web\Actions\SegmentPage\Segments\DeleteSegmentAction;
use App\Web\Actions\SegmentPage\Segments\GetSegmentsAction;
use App\Web\Actions\SegmentPage\Segments\UpdateSegmentAction;
use App\Web\Actions\SegmentPage\UpdateSegmentPageAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('segment-page', function (RouteCollectorProxy $group) {
        $group->get('', ListAllSegmentPagesAction::class);
        $group->post('', CreateSegmentPageAction::class)->add(new CheckRequiredFieldsMiddleware(['name']));
        $group->get('/{id}', GetSegmentPageByIdAction::class);
        $group->put('/{id}', UpdateSegmentPageAction::class);
        $group->delete('/{id}', DeleteSegmentPageAction::class);
        $group->group('/{id}/segment', function (RouteCollectorProxy $file) {
            $file->get('', GetSegmentsAction::class);
            $file->post('/file', CreateFileSegmentAction::class)->add(new CheckRequiredFieldsMiddleware([
                'file',
                'action',
                'position',
            ]));
            $file->post('/gallery', CreateGallerySegmentAction::class)->add(new CheckRequiredFieldsMiddleware([
                'gallery',
                'position',
            ]));
            $file->post('/form', CreateFormSegmentAction::class)->add(new CheckRequiredFieldsMiddleware([
                'form',
                'position',
            ]));
            $file->post('/html', CreateHtmlSegmentAction::class)->add(new CheckRequiredFieldsMiddleware([
                'html',
                'position',
            ]));
            $file->delete('/{position}', DeleteSegmentAction::class);
            $file->put('/{position}', UpdateSegmentAction::class);
        });
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};