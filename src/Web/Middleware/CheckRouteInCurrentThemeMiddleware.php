<?php

namespace App\Web\Middleware;

use App\Database;
use App\Database\MenuItem;
use App\Theming;
use App\Web\Actions\Action;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Throwable;

class CheckRouteInCurrentThemeMiddleware implements MiddlewareInterface
{

    private Engine $engine;

    /**
     * CheckRouteInCurrentThemeMiddleware constructor.
     * @param Engine $engine
     */
    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }


    /**
     * @inheritDoc
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

        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        $response = new Response();
        try {
            $this->engine->addData(['body' => $parsedBody, 'queryParams' => $queryParams]);
            $this->engine->loadExtension(new Theming\Theme(Database\Theme::getActiveTheme()));
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

    public function checkMenuItem(MenuItem $menuItem, string $path): bool
    {
        $result = $path === $menuItem->route;
        foreach ($menuItem->getItems() as $item) {
            $result |= $this->checkMenuItem($item, $path);
        }

        return $result;
    }
}