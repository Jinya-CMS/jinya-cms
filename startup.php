<?php

use Dotenv\Dotenv;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Utils\CacheUtils;
use Jinya\Cms\Web\Handlers\ErrorHandler;
use Jinya\Router\Extensions\JinyaDatabaseExtension;
use Nyholm\Psr7\Response;

require __DIR__ . '/defines.php';
require __DIR__ . '/vendor/autoload.php';

if (!is_dir(StorageBaseService::SAVE_PATH) && !mkdir(
    $concurrentDirectory = StorageBaseService::SAVE_PATH,
    0775,
    true
) && !is_dir($concurrentDirectory)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
}

function getRouterConfiguration(): array
{
    return [
        __JINYA_CACHE,
        __JINYA_CONTROLLERS,
        new Response(404),
        null,
        new JinyaDatabaseExtension(
            __JINYA_CACHE,
            __JINYA_ENTITY,
            new ErrorHandler()
        )
    ];
}

if (file_exists(__DIR__ . '/.env') || file_exists(__DIR__ . '/.env.dist')) {
    $dotenv = Dotenv::createUnsafeImmutable(__DIR__, ['.env', '.env.dist']);
    $dotenv->load();
}

JinyaConfiguration::getConfiguration()->reconfigureDatabase();

if (JinyaConfiguration::getConfiguration()->get('env', 'app', 'prod') === 'dev') {
    CacheUtils::recreateRoutingCache();
}
