<?php

use App\Web\Actions\Theme\ActivateThemeAction;
use App\Web\Actions\Theme\CompileThemeAction;
use App\Web\Actions\Theme\GetConfigurationStructureAction;
use App\Web\Actions\Theme\GetConfigurationValuesAction;
use App\Web\Actions\Theme\GetPreviewImageAction;
use App\Web\Actions\Theme\GetStyleVariablesAction;
use App\Web\Actions\Theme\GetThemeAction;
use App\Web\Actions\Theme\GetThemeFileAction;
use App\Web\Actions\Theme\GetThemeFormAction;
use App\Web\Actions\Theme\GetThemeGalleryAction;
use App\Web\Actions\Theme\GetThemeMenuAction;
use App\Web\Actions\Theme\GetThemePageAction;
use App\Web\Actions\Theme\GetThemeSegmentPageAction;
use App\Web\Actions\Theme\ListAllThemesAction;
use App\Web\Actions\Theme\PutConfigurationAction;
use App\Web\Actions\Theme\PutStyleVariablesAction;
use App\Web\Actions\Theme\PutThemeFileAction;
use App\Web\Actions\Theme\PutThemeFormAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('theme', function (RouteCollectorProxy $theme) {
        $theme->get('', ListAllThemesAction::class);
        $theme->group('/{id}', function (RouteCollectorProxy $id) {
            $id->get('', GetThemeAction::class);
            $id->get('/preview', GetPreviewImageAction::class);
            $id->put('/active', ActivateThemeAction::class);
            $id->put('/assets', CompileThemeAction::class);
            $id->group('/styling', function (RouteCollectorProxy $styling) {
                $styling->get('', GetStyleVariablesAction::class);
                $styling->put('', PutStyleVariablesAction::class);
            });
            $id->group('/file', function (RouteCollectorProxy $file) {
                $file->get('', GetThemeFileAction::class);
                $file->put('/{name}', PutThemeFileAction::class);
            });
            $id->group('/form', function (RouteCollectorProxy $form) {
                $form->get('', GetThemeFormAction::class);
                $form->put('/{name}', PutThemeFormAction::class);
            });
            $id->group('/gallery', function (RouteCollectorProxy $gallery) {
                $gallery->get('', GetThemeGalleryAction::class);
                $gallery->put('/{name}', PutStyleVariablesAction::class);
            });
            $id->group('/menu', function (RouteCollectorProxy $menu) {
                $menu->get('', GetThemeMenuAction::class);
                $menu->put('/{name}', PutStyleVariablesAction::class);
            });
            $id->group('/page', function (RouteCollectorProxy $page) {
                $page->get('', GetThemePageAction::class);
                $page->put('/{name}', PutStyleVariablesAction::class);
            });
            $id->group('/segment-page', function (RouteCollectorProxy $page) {
                $page->get('', GetThemeSegmentPageAction::class);
                $page->put('/{name}', PutStyleVariablesAction::class);
            });
            $id->group('/configuration', function (RouteCollectorProxy $config) {
                $config->get('/structure', GetConfigurationStructureAction::class);
                $config->get('', GetConfigurationValuesAction::class);
                $config->put('', PutConfigurationAction::class);
            });
        });
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};