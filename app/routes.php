<?php

use App\Routing\JinyaModelToRouteResolver;
use App\Web\Actions\Action;
use App\Web\Actions\ApiKey\DeleteApiKeyAction;
use App\Web\Actions\ApiKey\ListAllApiKeysAction;
use App\Web\Actions\Artist\ActivateArtistAction;
use App\Web\Actions\Artist\DeactivateArtistAction;
use App\Web\Actions\Artist\ProfilePicture\DeleteProfilePictureAction;
use App\Web\Actions\Artist\ProfilePicture\GetProfilePictureAction;
use App\Web\Actions\Artist\ProfilePicture\UploadProfilePictureAction;
use App\Web\Actions\Frontend\GetFrontAction;
use App\Web\Actions\Frontend\PostFrontAction;
use App\Web\Actions\Install\GetInstallerAction;
use App\Web\Actions\Install\PostInstallerAction;
use App\Web\Actions\Update\GetUpdateAction;
use App\Web\Actions\Update\PostUpdateAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRouteInCurrentThemeMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->map(['HEAD'], '/api/login', function (ServerRequestInterface $request, ResponseInterface $response) {
        return $response->withStatus(Action::HTTP_NO_CONTENT);
    })->add(AuthenticationMiddleware::class);
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
    $app->group('/update', function (RouteCollectorProxy $updater) {
        $updater->get('', GetUpdateAction::class);
        $updater->post('', PostUpdateAction::class);
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
    $app->group('/api', function (RouteCollectorProxy $proxy) {
        $proxy->map(['HEAD'], '/matomo', function (ServerRequestInterface $request, ResponseInterface $response) {
            if (getenv('MATOMO_API_KEY') && getenv('MATOMO_SERVER') && getenv('MATOMO_SITE_ID')) {
                return $response->withStatus(Action::HTTP_NO_CONTENT);
            }

            return $response->withStatus(Action::HTTP_NOT_FOUND);
        })->add(AuthenticationMiddleware::class);
        $proxy->group('/api_key', function (RouteCollectorProxy $proxy) {
            $proxy->get('', ListAllApiKeysAction::class);
            $proxy->delete('/{key}', DeleteApiKeyAction::class);
        })->add(AuthenticationMiddleware::class);
        $proxy->group('/artist/{id}/profilepicture', function (RouteCollectorProxy $proxy) {
            $proxy->get('', GetProfilePictureAction::class);
            $proxy->put('', UploadProfilePictureAction::class)->add(RoleMiddleware::ROLE_ADMIN);
            $proxy->delete('', DeleteProfilePictureAction::class)->add(RoleMiddleware::ROLE_ADMIN);
        });
        $proxy->group('/artist/{id}/activation', function (RouteCollectorProxy $proxy) {
            $proxy->put('', ActivateArtistAction::class);
            $proxy->delete('', DeactivateArtistAction::class);
        });
        $proxy->put('/me/profilepicture', fn(ServerRequestInterface $request, ResponseInterface $response, array $args) => (new UploadProfilePictureAction())($request, $response, ['id' => $request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST)->id]));

        $proxy->any('/{entity}[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            return JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, $args);
        });
        $proxy->any('/media/{entity}[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            return JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, $args);
        });
    });

    $app->group('/{route:.*}', function (RouteCollectorProxy $frontend) {
        $frontend->get('', GetFrontAction::class);
        $frontend->post('', PostFrontAction::class);
    })->add(CheckRouteInCurrentThemeMiddleware::class);
};
