<?php /** @noinspection StaticClosureCanBeUsedInspection */

use App\Authentication\AuthenticationChecker;
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
use App\Web\Actions\Blog\Post\ListPostsByCategoryAction;
use App\Web\Actions\Database\DatabaseAnalyzerAction;
use App\Web\Actions\Database\ExecuteQueryAction;
use App\Web\Actions\Environment\GetEnvironmentAction;
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
use App\Web\Actions\Gallery\Positions\CreatePositionAction;
use App\Web\Actions\Gallery\Positions\DeletePositionAction;
use App\Web\Actions\Gallery\Positions\GetPositionsAction;
use App\Web\Actions\Gallery\Positions\UpdatePositionAction;
use App\Web\Actions\Install\GetInstallerAction;
use App\Web\Actions\Install\PostInstallerAction;
use App\Web\Actions\KnownDevice\DeleteKnownDeviceAction;
use App\Web\Actions\KnownDevice\ListAllKnownDevicesAction;
use App\Web\Actions\KnownDevice\ValidateKnownDeviceAction;
use App\Web\Actions\LocateIp\LocatorAction;
use App\Web\Actions\Menu\Items\CreateMenuItemByMenuAction;
use App\Web\Actions\Menu\Items\CreateMenuItemByMenuItemAction;
use App\Web\Actions\Menu\Items\DeleteMenuItemAction;
use App\Web\Actions\Menu\Items\GetMenuItemsByMenuAction;
use App\Web\Actions\Menu\Items\MoveMenuItemParentToItemAction;
use App\Web\Actions\Menu\Items\ResetMenuItemParentAction;
use App\Web\Actions\Menu\Items\UpdateMenuItemAction;
use App\Web\Actions\MyJinya\GetArtistInfoAction;
use App\Web\Actions\MyJinya\UpdateAboutMeAction;
use App\Web\Actions\MyJinya\UpdateColorSchemeAction;
use App\Web\Actions\PhpInfo\GetPhpInfoAction;
use App\Web\Actions\SegmentPage\Segments\CreateFileSegmentAction;
use App\Web\Actions\SegmentPage\Segments\CreateFormSegmentAction;
use App\Web\Actions\SegmentPage\Segments\CreateGallerySegmentAction;
use App\Web\Actions\SegmentPage\Segments\CreateHtmlSegmentAction;
use App\Web\Actions\SegmentPage\Segments\DeleteSegmentAction;
use App\Web\Actions\SegmentPage\Segments\GetSegmentsAction;
use App\Web\Actions\SegmentPage\Segments\UpdateSegmentAction;
use App\Web\Actions\Statistics\GetEntityStatisticsAction;
use App\Web\Actions\Statistics\GetHistoryStatisticsAction;
use App\Web\Actions\Statistics\GetSystemStatisticsAction;
use App\Web\Actions\Statistics\GetVisitsByBrowserAction;
use App\Web\Actions\Statistics\GetVisitsByCountryAction;
use App\Web\Actions\Statistics\GetVisitsByDeviceBrandAction;
use App\Web\Actions\Statistics\GetVisitsByDeviceTypeAction;
use App\Web\Actions\Statistics\GetVisitsByLanguageAction;
use App\Web\Actions\Statistics\GetVisitsByOsAction;
use App\Web\Actions\Statistics\GetVisitsByReferrerAction;
use App\Web\Actions\Theme\ActivateThemeAction;
use App\Web\Actions\Theme\CompileThemeAction;
use App\Web\Actions\Theme\CreateThemeAction;
use App\Web\Actions\Theme\GetConfigurationStructureAction;
use App\Web\Actions\Theme\GetDefaultConfigurationValuesAction;
use App\Web\Actions\Theme\GetLinkItemAction;
use App\Web\Actions\Theme\GetPreviewImageAction;
use App\Web\Actions\Theme\GetStyleVariablesAction;
use App\Web\Actions\Theme\PutConfigurationAction;
use App\Web\Actions\Theme\PutLinkItemAction;
use App\Web\Actions\Theme\PutStyleVariablesAction;
use App\Web\Actions\Theme\UpdateThemeFilesAction;
use App\Web\Actions\Update\GetUpdateAction;
use App\Web\Actions\Update\InitUpdateProcessAction;
use App\Web\Actions\Update\PostUpdateAction;
use App\Web\Actions\Version\GetVersionInfo;
use App\Web\Middleware\AuthorizationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\CheckRouteInCurrentThemeMiddleware;
use GuzzleHttp\Psr7\MimeType;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
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
        })->add(AuthorizationMiddleware::class);

        // API Keys
        $proxy->group('/api_key', function (RouteCollectorProxy $proxy) {
            $proxy->get('', ListAllApiKeysAction::class);
            $proxy->delete('/{key}', DeleteApiKeyAction::class);
        })->add(AuthorizationMiddleware::class);

        // Artists
        $proxy->group('/artist/{id}/profilepicture', function (RouteCollectorProxy $proxy) {
            $proxy->get('', GetProfilePictureAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));
            $proxy->put('', UploadProfilePictureAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));
            $proxy->delete('', DeleteProfilePictureAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));
        });
        $proxy->group('/artist/{id}/activation', function (RouteCollectorProxy $proxy) {
            $proxy->put('', ActivateArtistAction::class);
            $proxy->delete('', DeactivateArtistAction::class);
        });
        $proxy->put('/me/profilepicture', fn(ServerRequestInterface $request, ResponseInterface $response, array $args) => (new UploadProfilePictureAction())($request, $response, ['id' => $request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST)->id]))
            ->add(AuthorizationMiddleware::class);

        // Authentication
        $proxy->post('/account/password', ChangePasswordAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['oldPassword', 'password']))
            ->add(AuthorizationMiddleware::class);
        $proxy->post('/login', LoginAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['username', 'password']));
        $proxy->map(['HEAD'], '/login', fn(ServerRequestInterface $request, ResponseInterface $response) => $response->withStatus(Action::HTTP_NO_CONTENT))
            ->add(AuthorizationMiddleware::class);
        $proxy->post('/2fa', TwoFactorAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['username', 'password']));

        // Blog
        $proxy->put('/blog/post/{id}/segment', BatchSegmentsAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['segments']))
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
        $proxy->get('/blog/category/{id}/post', ListPostsByCategoryAction::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));
        $proxy->get('/blog/post/{id}/segment', \App\Web\Actions\Blog\Post\GetSegmentsAction::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));

        // Database
        $proxy->get('/maintenance/database/analyze', DatabaseAnalyzerAction::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));
        $proxy->post('/maintenance/database/query', ExecuteQueryAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['query']))
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));

        // Environment
        $proxy->get('/environment', GetEnvironmentAction::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));

        // File
        $proxy->group('/media/file/{id}/content', function (RouteCollectorProxy $proxy) {
            $proxy->put('/finish', FinishUploadAction::class)->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->put('/{position}', UploadChunkAction::class)->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->put('', StartUploadAction::class)->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->get('', GetFileContentAction::class);
        });

        // Form Items
        $proxy->group('/form/{id}/item', function (RouteCollectorProxy $proxy) {
            $proxy->get('', GetFormItemsAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));
            $proxy->delete('/{position}', DeleteFormItemAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->post('', CreateFormItemAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['label', 'position']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->put('/{position}', UpdateFormItemAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
        });

        // Gallery Positions
        $proxy->group('/media/gallery/{galleryId}/file', function (RouteCollectorProxy $proxy) {
            $proxy->get('', GetPositionsAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));
            $proxy->delete('/{position}', DeletePositionAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->post('', CreatePositionAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['file', 'position']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->put('/{position}', UpdatePositionAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
        });

        // Known Devices
        $proxy->group('/known_device', function (RouteCollectorProxy $proxy) {
            $proxy->get('', ListAllKnownDevicesAction::class)->add(AuthorizationMiddleware::class);
            $proxy->delete('/{key}', DeleteKnownDeviceAction::class)->add(AuthorizationMiddleware::class);
            $proxy->map(['HEAD'], '/{key}', ValidateKnownDeviceAction::class);
        });

        // Locate IP
        $proxy->get('/ip-location/{ip}', LocatorAction::class)->add(AuthorizationMiddleware::class);

        // Menu Items
        $proxy->group('/menu/{id}/item', function (RouteCollectorProxy $proxy) {
            $proxy->post('', CreateMenuItemByMenuAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['position', 'title']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->get('', GetMenuItemsByMenuAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));
            $proxy->put('/{menuItemId}/move/parent/one/level/up', MoveMenuItemParentToItemAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
        })->add(AuthorizationMiddleware::class);
        $proxy->group('/menu-item/{menuItemId}', function (RouteCollectorProxy $proxy) {
            $proxy->post('/item', CreateMenuItemByMenuItemAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['position', 'title']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->put('/move/parent/to/item/{newParent}', MoveMenuItemParentToItemAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->put('', UpdateMenuItemAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->delete('', DeleteMenuItemAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
        });
        $proxy->put('/menu/{menuItemId}/move/parent/to/menu/{menuId}', ResetMenuItemParentAction::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));

        // My Jinya
        $proxy->get('/me', GetArtistInfoAction::class)->add(AuthorizationMiddleware::class);
        $proxy->get('/account', GetArtistInfoAction::class)->add(AuthorizationMiddleware::class);
        $proxy->put('/me', UpdateAboutMeAction::class)->add(AuthorizationMiddleware::class);
        $proxy->put('/me/colorscheme', UpdateColorSchemeAction::class)
            ->add(new CheckRequiredFieldsMiddleware(['colorScheme']))
            ->add(AuthorizationMiddleware::class);

        // PHP Info
        $proxy->get('/phpinfo', GetPhpInfoAction::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));

        // Segment page
        $proxy->group('/segment-page/{id}/segment', function (RouteCollectorProxy $proxy) {
            $proxy->post('/file', CreateFileSegmentAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['file', 'action', 'position']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->post('/form', CreateFormSegmentAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['form', 'position']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->post('/gallery', CreateGallerySegmentAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['gallery', 'position']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->post('/html', CreateHtmlSegmentAction::class)
                ->add(new CheckRequiredFieldsMiddleware(['html', 'position']))
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->delete('/{position}', DeleteSegmentAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->put('/{position}', UpdateSegmentAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));
            $proxy->get('', GetSegmentsAction::class)
                ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));
        });

        // Statistics
        $proxy->group('/statistics', function (RouteCollectorProxy $proxy) {
            $proxy->get('/entity', GetEntityStatisticsAction::class);
            $proxy->get('/history/{type}', GetHistoryStatisticsAction::class);
            $proxy->get('/system', GetSystemStatisticsAction::class);
            $proxy->group('/visits', function (RouteCollectorProxy $proxy) {
                $proxy->get('/browser', GetVisitsByBrowserAction::class);
                $proxy->get('/country', GetVisitsByCountryAction::class);
                $proxy->get('/brand', GetVisitsByDeviceBrandAction::class);
                $proxy->get('/type', GetVisitsByDeviceTypeAction::class);
                $proxy->get('/language', GetVisitsByLanguageAction::class);
                $proxy->get('/os', GetVisitsByOsAction::class);
                $proxy->get('/referrer', GetVisitsByReferrerAction::class);
            });
        })->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_READER));

        // Theme
        $proxy->get('/theme/{id}/preview', GetPreviewImageAction::class);
        $proxy->group('/theme', function (RouteCollectorProxy $proxy) {
            $proxy->post('', CreateThemeAction::class);
            $proxy->put('/{id}', UpdateThemeFilesAction::class);
            $proxy->put('/{id}/active', ActivateThemeAction::class);
            $proxy->put('/{id}/assets', CompileThemeAction::class);
            $proxy->get('/{id}/styling', GetStyleVariablesAction::class);
            $proxy->put('/{id}/styling', PutStyleVariablesAction::class);

            $proxy->get('/{id}/{entityType}', GetLinkItemAction::class);
            $proxy->put('/{id}/{entityType}/{name}', PutLinkItemAction::class);

            $proxy->group('/{id}/configuration', function (RouteCollectorProxy $proxy) {
                $proxy->get('/structure', GetConfigurationStructureAction::class);
                $proxy->get('/default', GetDefaultConfigurationValuesAction::class);

                $proxy->put('', PutConfigurationAction::class);
            });
        })->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_WRITER));

        // Update
        $proxy->put('/update', InitUpdateProcessAction::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));

        // Version
        $proxy->get('/version', GetVersionInfo::class)
            ->add(new AuthorizationMiddleware(AuthenticationChecker::ROLE_ADMIN));
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
    $app->any('/api/page[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $pageArgs = $args;
        $pageArgs['entity'] = 'SimplePage';
        return JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, $pageArgs);
    });
    $app->any('/api/{entity}[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        return JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, $args);
    });

    $app->get('/jinya-designer[/{file:.*}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        if (array_key_exists('file', $args)) {
            $mime = MimeType::fromFilename(__ROOT__ . '/jinya-designer/' . $args['file']);
            return $response
                ->withAddedHeader('Content-Type', $mime)
                ->withBody(Stream::create(fopen(__ROOT__ . '/jinya-designer/' . $args['file'], 'rb')));
        }

        return $response
            ->withAddedHeader('Content-Type', 'text/html')
            ->withBody(Stream::create(fopen(__ROOT__ . '/jinya-designer/index.html', 'rb')));
    });

    // Frontend
    $app->get('/{year:\d\d\d\d}/{month:\d\d}/{day:\d\d}/{slug}', GetBlogFrontAction::class);
    $app->get('/', GetFrontAction::class);
    $app->group('/{route:.*}', function (RouteCollectorProxy $frontend) {
        $frontend->get('', GetFrontAction::class);
        $frontend->post('', PostFrontAction::class);
    })->add(CheckRouteInCurrentThemeMiddleware::class);
};
