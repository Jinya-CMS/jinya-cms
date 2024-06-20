<?php

declare(strict_types=1);

use function Jinya\Router\handle_request;

require __DIR__ . '/../startup.php';

if (function_exists('getRouterConfiguration')) {
    handle_request(...getRouterConfiguration());
}
