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
        $environment = require __DIR__ . '/routing/api/environment.php';
        $gallery = require __DIR__ . '/routing/api/gallery.php';
        $form = require __DIR__ . '/routing/api/form.php';
        $segmentPage = require __DIR__ . '/routing/api/segmentPage.php';
        $menu = require __DIR__ . '/routing/api/menu.php';
        $database = require __DIR__ . '/routing/api/database.php';
        $message = require __DIR__ . '/routing/api/message.php';

        $artistRoutes($api);
        $authenticationRoutes($api);
        $fileRoutes($api);
        $simplePageRoutes($api);
        $myJinyaRoutes($api);
        $knownDeviceRoutes($api);
        $phpInfo($api);
        $environment($api);
        $gallery($api);
        $form($api);
        $segmentPage($api);
        $menu($api);
        $database($api);
        $message($api);
    });
};
