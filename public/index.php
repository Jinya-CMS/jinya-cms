<?php

declare(strict_types=1);


use function Jinya\Router\handle_request;

require __DIR__ . '/../startup.php';

global $__JINYA_ROUTER_CONFIGURATION;

handle_request(...$__JINYA_ROUTER_CONFIGURATION);
