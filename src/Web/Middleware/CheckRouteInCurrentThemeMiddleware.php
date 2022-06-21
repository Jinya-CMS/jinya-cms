<?php

namespace App\Web\Middleware;

use App\Database;
use App\Database\MenuItem;
use App\Theming;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;

/**
 * Middleware to check if the current route is in any configured menu for the current theme
 */
class CheckRouteInCurrentThemeMiddleware implements MiddlewareInterface
{
    /** @var Engine The template engine */
    private Engine $engine;

    /**
     * CheckRouteInCurrentThemeMiddleware constructor.
     */
    public function __construct()
    {
        $this->engine = Theming\Engine::getPlatesEngine();
    }

    /**
     * Processes the middleware, during processing the request the current request URI is being parsed and checked if it is available in the currently activated theme. If yes, the FrontController is invoked. If not, the error handler is invoked returning either an error page or the homepage, depending on the theme configuration.
     *
     * Before the error handler is invoked, the blog posts will be searched for the slug
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = new Response();

        try {
            $uri = $request->getUri();
            $activeTheme = Database\Theme::getActiveTheme();
            if ($activeTheme === null) {
                throw new RuntimeException('No active theme found');
            }

            $menus = $activeTheme->getMenus();
            $path = substr($uri->getPath(), 1);
            foreach ($menus as $menu) {
                foreach ($menu->getItems() as $item) {
                    if ($this->checkMenuItem($item, $path)) {
                        return $handler->handle($request);
                    }
                }
            }

            $blogPost = Database\BlogPost::findBySlug($path);
            if ($blogPost !== null) {
                return $handler->handle($request);
            }

            $activeThemingTheme = new Theming\Theme($activeTheme);
            if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $activeThemingTheme->getErrorBehavior()) {
                $redirectUri = $uri->getScheme() . '://' . $uri->getHost();
                if (($uri->getScheme() === 'https' && $uri->getPort() !== 443) || ($uri->getScheme() === 'http' && $uri->getPort() !== 80)) {
                    $redirectUri .= ':' . $uri->getPort();
                }

                return $response
                    ->withHeader('Location', $redirectUri)
                    ->withStatus(302);
            }

            $parsedBody = $request->getParsedBody();
            $queryParams = $request->getQueryParams();
            $this->engine->addData(['body' => $parsedBody, 'queryParams' => $queryParams]);
            $this->engine->loadExtension($activeThemingTheme);
            $this->engine->loadExtension(new Theming\Extensions\MenuExtension());
            $this->engine->loadExtension(new URI($request->getUri()->getPath()));

            $renderResult = $this->engine->render('theme::404');
            $response->getBody()->write($renderResult);

            return $response
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(Action::HTTP_NOT_FOUND);
        } catch (Throwable $exception) {
            $renderResult = $this->engine->render('emergency::500', ['exception' => $exception]);
            $response->getBody()->write($renderResult);

            return $response
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(Action::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Checks if the given menu item matches the path
     *
     * @throws Database\Exceptions\UniqueFailedException
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function checkMenuItem(MenuItem $menuItem, string $path): bool|int
    {
        $result = $path === $menuItem->route;
        foreach ($menuItem->getItems() as $item) {
            $result |= $this->checkMenuItem($item, $path);
        }

        return $result;
    }
}
