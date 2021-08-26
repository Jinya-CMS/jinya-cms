<?php

namespace App\Web\Middleware;

use App\Database;
use App\Database\MenuItem;
use App\Theming;
use App\Web\Actions\Action;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class CheckRouteInCurrentThemeMiddleware implements MiddlewareInterface
{
    private Engine $engine;

    /**
     * CheckRouteInCurrentThemeMiddleware constructor.
     */
    public function __construct()
    {
        $this->engine = Theming\Engine::getPlatesEngine();
    }

    /**
     * {@inheritDoc}
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $activeTheme = Database\Theme::getActiveTheme();
        $menus = $activeTheme->getMenus();
        $path = substr($uri->getPath(), 1);
        foreach ($menus as $menu) {
            foreach ($menu->getItems() as $item) {
                if ($this->checkMenuItem($item, $path)) {
                    return $handler->handle($request);
                }
            }
        }

        $activeThemingTheme = new Theming\Theme($activeTheme);
        $response = new Response();

        if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $activeThemingTheme->getErrorBehavior()) {
            $redirectUri = $uri->getScheme() . '://' . $uri->getHost();
            if (('https' === $uri->getScheme() && 443 !== $uri->getPort()) || ('http' === $uri->getScheme() && 80 !== $uri->getPort())) {
                $redirectUri .= ':' . $uri->getPort();
            }

            return $response
                ->withHeader('Location', $redirectUri)
                ->withStatus(302);
        }

        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        try {
            $this->engine->addData(['body' => $parsedBody, 'queryParams' => $queryParams]);
            $this->engine->loadExtension($activeThemingTheme);
            $this->engine->loadExtension(new Theming\MenuExtension());
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
     * @throws Database\Exceptions\UniqueFailedException
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     */
    public function checkMenuItem(MenuItem $menuItem, string $path): bool|int|float
    {
        $result = $path === $menuItem->route;
        foreach ($menuItem->getItems() as $item) {
            $result |= $this->checkMenuItem($item, $path);
        }

        return $result;
    }
}
