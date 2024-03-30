<?php

declare(strict_types=1);

use Jinya\Cms\Web\Handlers\ErrorHandler;
use Jinya\Router\Extensions\JinyaDatabaseExtension;
use Nyholm\Psr7\Response;

use function Jinya\Router\build_routing_table;
use function Jinya\Router\handle_request;

require __DIR__ . '/../startup.php';

if (getenv('APP_ENV') === 'dev') {
    build_routing_table(
        __JINYA_CACHE,
        __JINYA_CONTROLLERS,
        new JinyaDatabaseExtension(
            __JINYA_CACHE,
            __JINYA_ENTITY,
            new ErrorHandler()
        )
    );
}

handle_request(
    __JINYA_CACHE,
    __JINYA_CONTROLLERS,
    new Response(404),
    extensions: new JinyaDatabaseExtension(
        __JINYA_CACHE,
        __JINYA_ENTITY,
        new ErrorHandler()
    )
);
