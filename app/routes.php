<?php

use App\Web\Actions\Action;
use App\Web\Actions\Frontend\GetFrontAction;
use App\Web\Actions\Frontend\GetHomeAction;
use App\Web\Actions\Frontend\PostFrontAction;
use App\Web\Actions\Install\GetInstallerAction;
use App\Web\Actions\Install\PostInstallerAction;
use App\Web\Actions\Update\GetUpdateAction;
use App\Web\Actions\Update\PostUpdateAction;
use App\Web\Middleware\CheckRouteInCurrentThemeMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/api/', function (RouteCollectorProxy $api) {
        $artistRoutes = require __DIR__ . '/routing/api/artist.php';
        $authenticationRoutes = require __DIR__ . '/routing/api/authentication.php';
        $fileRoutes = require __DIR__ . '/routing/api/file.php';
        $simplePageRoutes = require __DIR__ . '/routing/api/simplePage.php';
        $myJinyaRoutes = require __DIR__ . '/routing/api/myjinya.php';
        $knownDeviceRoutes = require __DIR__ . '/routing/api/knownDevice.php';
        $phpInfo = require __DIR__ . '/routing/api/phpinfo.php';
        $environment = require __DIR__ . '/routing/api/environment.php';
        $gallery = require __DIR__ . '/routing/api/gallery.php';
        $form = require __DIR__ . '/routing/api/form.php';
        $segmentPage = require __DIR__ . '/routing/api/segmentPage.php';
        $menu = require __DIR__ . '/routing/api/menu.php';
        $database = require __DIR__ . '/routing/api/database.php';
        $message = require __DIR__ . '/routing/api/message.php';
        $theme = require __DIR__ . '/routing/api/theme.php';

        $artistRoutes($api);
        $authenticationRoutes($api);
        $fileRoutes($api);
        $simplePageRoutes($api);
        $myJinyaRoutes($api);
        $knownDeviceRoutes($api);
        $phpInfo($api);
        $environment($api);
        $gallery($api);
        $form($api);
        $segmentPage($api);
        $menu($api);
        $database($api);
        $message($api);
        $theme($api);
    });
    $app->get('/', GetHomeAction::class);
    $app->group('/installer', function (RouteCollectorProxy $installer) {
        $installer->get('', GetInstallerAction::class);
        $installer->post('', PostInstallerAction::class);
    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        if (file_exists(__ROOT__ . '/installed.lock')) {
            return (new Response())
                ->withStatus(Action::HTTP_MOVED_PERMANENTLY)
                ->withHeader('Location', '/');
        }

        return $handler->handle($request);
    });
    $app->group('/update', function (RouteCollectorProxy $installer) {
        $installer->get('', GetUpdateAction::class);
        $installer->post('', PostUpdateAction::class);
    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $cookies = $request->getCookieParams();
        $updateLock = __ROOT__ . '/update.lock';
        if (isset($cookies['JinyaUpdateKey'])
            && file_exists($updateLock)
            && file_get_contents($updateLock) === $cookies['JinyaUpdateKey']) {
            return $handler->handle($request);
        }

        return (new Response())
            ->withStatus(Action::HTTP_MOVED_PERMANENTLY)
            ->withHeader('Location', '/');
    });
    $app->group('/{route:.*}', function (RouteCollectorProxy $frontend) {
        $frontend->get('', GetFrontAction::class);
        $frontend->post('', PostFrontAction::class);
    })->add(CheckRouteInCurrentThemeMiddleware::class);
};
