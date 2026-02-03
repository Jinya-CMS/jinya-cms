<?php

declare(strict_types=1);

use function Jinya\Cms\Images\handle_images;
use function Jinya\Router\handle_request;

require_once __DIR__ . '/startup.php';

$maxRequests = (int)($_SERVER['MAX_REQUESTS'] ?? 0);
for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
    if (function_exists('getRouterConfiguration')) {
        $keepRunning = frankenphp_handle_request(
            static function () {
                error_log("Worker hit path " . $_SERVER['REQUEST_URI']);
                if (str_starts_with($_SERVER['REQUEST_URI'], '/image.php')) {
                    handle_images(false);
                    return;
                }

                handle_request(...getRouterConfiguration());
            }
        );

        // Call the garbage collector to reduce the chances of it being triggered in the middle of a page generation
        gc_collect_cycles();

        if (!$keepRunning) {
            break;
        }
    }
}
