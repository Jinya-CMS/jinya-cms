<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Log\LogLevel;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions(
        [
            'settings' => [
                'displayErrorDetails' => 'dev' === getenv('APP_ENV'),
                'logger' => [
                    'name' => 'jinya-backend',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => getenv('LOGLEVEL') ?: LogLevel::DEBUG,
                ],
            ],
        ]
    );
};
