<?php

use Dotenv\Dotenv;

use Jinya\Cms\Configuration\JinyaConfiguration;

require __DIR__ . '/defines.php';
require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env') || file_exists(__DIR__ . '/.env.dist')) {
    $dotenv = Dotenv::createUnsafeImmutable(__DIR__, ['.env', '.env.dist']);
    $dotenv->load();
}

JinyaConfiguration::getConfiguration()->reconfigureDatabase();
