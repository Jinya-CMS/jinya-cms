<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use League\Plates\Engine;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            LoggerInterface::class => function (ContainerInterface $c): LoggerInterface {
                $settings = [
                    'displayErrorDetails' => 'dev' === getenv('APP_ENV'),
                    'logger' => [
                        'name' => 'jinya-backend',
                        'path' => isset($_ENV['DOCKER']) ? 'php://stdout' : __ROOT__ . '/logs/app.log',
                        'level' => getenv('LOGLEVEL') ?: LogLevel::INFO,
                    ],
                ];

                $loggerSettings = $settings['logger'];
                $logger = new Logger($loggerSettings['name']);

                $processor = new UidProcessor();
                $logger->pushProcessor($processor);

                $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
                $logger->pushHandler($handler);

                return $logger;
            },
            Engine::class => function (): Engine {
                $engine = new Engine();
                $engine->addFolder('mailing', __DIR__ . '/../src/Mailing/Templates');
                $engine->addFolder('emergency', __DIR__ . '/../src/Emergency/Templates');
                $engine->setFileExtension('phtml');

                return $engine;
            },
        ]
    );
};
