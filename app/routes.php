<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/api/', function (RouteCollectorProxy $api) {
        $artistRoutes = require __DIR__ . '/routing/api/artist.php';
        $authenticationRoutes = require __DIR__ . '/routing/api/authentication.php';
        $fileRoutes = require __DIR__ . '/routing/api/file.php';
        $simplePageRoutes = require __DIR__ . '/routing/api/simplePage.php';
        $myJinyaRoutes = require __DIR__ . '/routing/api/myjinya.php';
        $knownDeviceRoutes = require __DIR__ . '/routing/api/knownDevice.php';
        $phpInfo = require __DIR__ . '/routing/api/phpinfo.php';

        $artistRoutes($api);
        $authenticationRoutes($api);
        $fileRoutes($api);
        $simplePageRoutes($api);
        $myJinyaRoutes($api);
        $knownDeviceRoutes($api);
        $phpInfo($api);
    });
};
