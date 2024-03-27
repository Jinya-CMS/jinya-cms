<?php

namespace App\Web\Middleware;

use Jinya\Router\Http\AbstractController;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class RedirectInstallerMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (file_exists(__ROOT__ . '/installed.lock')) {
            return new Response(AbstractController::HTTP_MOVED_PERMANENTLY, ['Location' => '/']);
        }

        return $handler->handle($request);
    }
}
