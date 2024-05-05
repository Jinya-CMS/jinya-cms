<?php

namespace Jinya\Cms\Web\Middleware;

use Exception;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\ThemeSyncer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

readonly class ThemeSyncMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;
    private ThemeSyncer $themeSyncer;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
        $this->themeSyncer = new ThemeSyncer();
    }


    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->themeSyncer->syncThemes();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $handler->handle($request);
    }
}
