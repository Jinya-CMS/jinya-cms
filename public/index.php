<?php

declare(strict_types=1);

use function Jinya\Router\handle_request;

require __DIR__ . '/../startup.php';

handle_request(...getRouterConfiguration());
