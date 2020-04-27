<?php

use App\Web\Actions\File\CreateFileAction;
use App\Web\Actions\File\DeleteFileAction;
use App\Web\Actions\File\GetFileByIdAction;
use App\Web\Actions\File\ListAllFilesAction;
use App\Web\Actions\File\UpdateFileAction;
use App\Web\Actions\File\Upload\FinishUploadAction;
use App\Web\Actions\File\Upload\StartUploadAction;
use App\Web\Actions\File\Upload\UploadChunkAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('file', function (RouteCollectorProxy $group) {
        $group->get('', ListAllFilesAction::class);
        $group->post('', CreateFileAction::class)->add(new CheckRequiredFieldsMiddleware(['name']));

        $group->get('/{id}', GetFileByIdAction::class);
        $group->put('/{id}', UpdateFileAction::class);
        $group->delete('/{id}', DeleteFileAction::class);

        $group->put('/{id}/content', StartUploadAction::class);
        $group->put('/{id}/content/finish', FinishUploadAction::class);
        $group->put('/{id}/content/{position}', UploadChunkAction::class);
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};