<?php

use App\Web\Actions\Action;
use App\Web\Actions\Frontend\GetFrontAction;
use App\Web\Actions\Frontend\PostFrontAction;
use App\Web\Actions\Install\GetInstallerAction;
use App\Web\Actions\Install\PostInstallerAction;
use App\Web\Actions\Update\GetUpdateAction;
use App\Web\Actions\Update\PostUpdateAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRouteInCurrentThemeMiddleware;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->map(
        ['HEAD'],
        '/api/login',
        function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response->withStatus(Action::HTTP_NO_CONTENT);
        }
    )->add(AuthenticationMiddleware::class);
    $app->group(
        '/installer',
        function (RouteCollectorProxy $installer) {
            $installer->get('', GetInstallerAction::class);
            $installer->post('', PostInstallerAction::class);
        }
    )->add(
        function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            if (file_exists(__ROOT__ . '/installed.lock')) {
                return (new Response())
                    ->withStatus(Action::HTTP_MOVED_PERMANENTLY)
                    ->withHeader('Location', '/');
            }

            return $handler->handle($request);
        }
    );
    $app->group(
        '/update',
        function (RouteCollectorProxy $updater) {
            $updater->get('', GetUpdateAction::class);
            $updater->post('', PostUpdateAction::class);
        }
    )->add(
        function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
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
        }
    );
    $app->map(['HEAD'],
        '/api/matomo',
        function (ServerRequestInterface $request, ResponseInterface $response) {
            if (getenv('MATOMO_API_KEY') && getenv('MATOMO_SERVER') && getenv('MATOMO_SITE_ID')) {
                return $response->withStatus(Action::HTTP_NO_CONTENT);
            }

            return $response->withStatus(Action::HTTP_NOT_FOUND);
        }
    )->add(AuthenticationMiddleware::class);
    $app->group(
        '/{route:.*}',
        function (RouteCollectorProxy $frontend) {
            $frontend->get('', GetFrontAction::class);
            $frontend->post('', PostFrontAction::class);
        }
    )->add(CheckRouteInCurrentThemeMiddleware::class);
};
