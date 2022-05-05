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
use App\Web\Actions\Authentication\ChangePasswordAction;
use App\Web\Actions\Authentication\LoginAction;
use App\Web\Actions\Authentication\TwoFactorAction;
use App\Web\Actions\Blog\Post\BatchSegmentsAction;
use App\Web\Actions\Blog\Post\CreatePostAction;
use App\Web\Actions\Blog\Post\ListPostsByCategoryAction;
use App\Web\Actions\Blog\Post\UpdatePostAction;
use App\Web\Actions\Database\DatabaseAnalyzerAction;
use App\Web\Actions\Database\ExecuteQueryAction;
use App\Web\Actions\File\GetFileContentAction;
use App\Web\Actions\File\Upload\FinishUploadAction;
use App\Web\Actions\File\Upload\StartUploadAction;
use App\Web\Actions\File\Upload\UploadChunkAction;
use App\Web\Actions\Form\Items\CreateFormItemAction;
use App\Web\Actions\Form\Items\DeleteFormItemAction;
use App\Web\Actions\Form\Items\GetFormItemsAction;
use App\Web\Actions\Form\Items\UpdateFormItemAction;
use App\Web\Actions\Frontend\GetBlogFrontAction;
use App\Web\Actions\Frontend\GetFrontAction;
use App\Web\Actions\Frontend\PostFrontAction;
use App\Web\Actions\Install\GetInstallerAction;
use App\Web\Actions\Install\PostInstallerAction;
use App\Web\Actions\Update\GetUpdateAction;
use App\Web\Actions\Update\PostUpdateAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\CheckRouteInCurrentThemeMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
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
        // Matomo
        $proxy->map(['HEAD'], '/matomo', function (ServerRequestInterface $request, ResponseInterface $response) {
            if (getenv('MATOMO_API_KEY') && getenv('MATOMO_SERVER') && getenv('MATOMO_SITE_ID')) {
                return $response->withStatus(Action::HTTP_NO_CONTENT);
            }

            return $response->withStatus(Action::HTTP_NOT_FOUND);
        })->add(AuthenticationMiddleware::class);

        // API Keys
        $proxy->group('/api_key', function (RouteCollectorProxy $proxy) {
            $proxy->get('', ListAllApiKeysAction::class);
            $proxy->delete('/{key}', DeleteApiKeyAction::class);
        })->add(AuthenticationMiddleware::class);

        // Artists
        $proxy->group('/artist/{id}/profilepicture', function (RouteCollectorProxy $proxy) {
            $proxy->get('', GetProfilePictureAction::class)
                ->add(new RoleMiddleware(RoleMiddleware::ROLE_READER))
                ->add(AuthenticationMiddleware::class);
            $proxy->put('', UploadProfilePictureAction::class)
                ->add(new RoleMiddleware(RoleMiddleware::ROLE_ADMIN))
                ->add(AuthenticationMiddleware::class);
            $proxy->delete('', DeleteProfilePictureAction::class)
                ->add(new RoleMiddleware(RoleMiddleware::ROLE_ADMIN))
                ->add(AuthenticationMiddleware::class);
        });
        $proxy->group('/artist/{id}/activation', function (RouteCollectorProxy $proxy) {
            $proxy->put('', ActivateArtistAction::class);
            $proxy->delete('', DeactivateArtistAction::class);
        });
        $proxy->put('/me/profilepicture', fn(ServerRequestInterface $request, ResponseInterface $response, array $args) => (new UploadProfilePictureAction())($request, $response, ['id' => $request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST)->id]))
            ->add(AuthenticationMiddleware::class);

        // Authentication
        $proxy->post('/account/password', ChangePasswordAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['oldPassword', 'password']))
            ->add(AuthenticationMiddleware::class);
        $proxy->post('/login', LoginAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['username', 'password']));
        $proxy->map(['HEAD'], '/api/login', fn(ServerRequestInterface $request, ResponseInterface $response) => $response->withStatus(Action::HTTP_NO_CONTENT))
            ->add(AuthenticationMiddleware::class);
        $proxy->post('/2fa', TwoFactorAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['username', 'password']));

        // Blog
        $proxy->put('/blog/post/{id}/segment', BatchSegmentsAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['segments']))
            ->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))
            ->add(AuthenticationMiddleware::class);
        $proxy->get('/blog/category/{id}/post', ListPostsByCategoryAction::class)
            ->add(new RoleMiddleware(RoleMiddleware::ROLE_READER))
            ->add(AuthenticationMiddleware::class);
        $proxy->post('/blog/post', CreatePostAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['title', 'slug']))
            ->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))
            ->add(AuthenticationMiddleware::class);
        $proxy->put('/blog/post/{id}', UpdatePostAction::class)
            ->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))
            ->add(AuthenticationMiddleware::class);
        $proxy->get('/blog/post/{id}/segment', UpdatePostAction::class)
            ->add(new RoleMiddleware(RoleMiddleware::ROLE_READER))
            ->add(AuthenticationMiddleware::class);

        // Database
        $proxy->get('/maintenance/database/analyze', DatabaseAnalyzerAction::class)
            ->add(new RoleMiddleware(RoleMiddleware::ROLE_ADMIN))
            ->add(AuthenticationMiddleware::class);
        $proxy->get('/maintenance/database/query', ExecuteQueryAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['query']))
            ->add(new RoleMiddleware(RoleMiddleware::ROLE_ADMIN))
            ->add(AuthenticationMiddleware::class);

        // File
        $proxy->group('/media/file/{id}/content', function (RouteCollectorProxy $proxy) {
            $proxy->put('/finish', FinishUploadAction::class);
            $proxy->put('/{position}', UploadChunkAction::class);
            $proxy->put('', StartUploadAction::class);
            $proxy->get('', GetFileContentAction::class);
        })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);

        // Form Items
        $proxy->group('/form/{id}/item', function (RouteCollectorProxy $proxy) {
            $proxy->get('', GetFormItemsAction::class)
                ->add(new RoleMiddleware(RoleMiddleware::ROLE_READER))
                ->add(AuthenticationMiddleware::class);
            $proxy->delete('/{position}', DeleteFormItemAction::class)
                ->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))
                ->add(AuthenticationMiddleware::class);
            $proxy->post('', CreateFormItemAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['label', 'position']))
                ->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))
                ->add(AuthenticationMiddleware::class);
            $proxy->put('/{position}', UpdateFormItemAction::class)
                ->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))
                ->add(AuthenticationMiddleware::class);
        });
    })->add(new BodyParsingMiddleware());

    // Reflection based
    $app->any('/api/media/{entity}[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        return JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, $args);
    });
    $app->any('/api/blog/{entity}[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $blogArgs = $args;
        $blogArgs['entity'] = 'Blog' . ucfirst($blogArgs['entity']);
        return JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, $blogArgs);
    });
    $app->any('/api/{entity}[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        return JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, $args);
    });

    $app->get('/{year:\d\d\d\d}/{month:\d\d}/{day:\d\d}/{slug}', GetBlogFrontAction::class);
    $app->group('/{route:.*}', function (RouteCollectorProxy $frontend) {
        $frontend->get('', GetFrontAction::class);
        $frontend->post('', PostFrontAction::class);
    })->add(CheckRouteInCurrentThemeMiddleware::class);
};
