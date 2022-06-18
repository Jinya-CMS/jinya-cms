<?php

namespace App\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Simple factory class to create a PSR-3 logger
 */
abstract class Logger
{
    /**
     * Creates a new PSR-3 logger, based on Monolog
     *
     * @return LoggerInterface
     */
    public static function getLogger(): LoggerInterface
    {
        $path = isset($_ENV['DOCKER']) ? 'php://stdout' : __ROOT__ . '/logs/app.log';
        /** @var 100|200|250|300|400|500|550|600|'ALERT'|'alert'|'CRITICAL'|'critical'|'DEBUG'|'debug'|'EMERGENCY'|'emergency'|'ERROR'|'error'|'INFO'|'info'|'NOTICE'|'notice'|'WARNING'|'warning' $level */
        $level = getenv('LOGLEVEL') ?: LogLevel::INFO;
        $logger = new \Monolog\Logger('jinya-cms');

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($path, $level);
        $logger->pushHandler($handler);

        return $logger;
    }
}