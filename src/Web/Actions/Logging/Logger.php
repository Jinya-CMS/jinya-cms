<?php

namespace App\Web\Actions\Logging;

use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;

class Logger
{
    public static function getLogger(): LoggerInterface
    {
        $path = isset($_ENV['DOCKER']) ? 'php://stdout' : __ROOT__ . '/logs/app.log';
        $level = getenv('LOGLEVEL') ?: LogLevel::INFO;
        $logger = new \Monolog\Logger('jinya-cms');

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($path, $level);
        $logger->pushHandler($handler);

        return $logger;
    }
}