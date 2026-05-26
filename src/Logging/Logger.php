<?php

namespace Jinya\Cms\Logging;

use Jinya\Cms\Configuration\JinyaConfiguration;
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
        $appEnv = JinyaConfiguration::getConfiguration()->get('env', 'app', 'prod');
        $defaultLevel = $appEnv === 'dev' ? LogLevel::DEBUG : LogLevel::INFO;
        $level = JinyaConfiguration::getConfiguration()->get('log', 'app', 'info') ?: $defaultLevel;
        $logger = new \Monolog\Logger('jinya-cms');

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        /** @phpstan-ignore argument.type */
        $handler = new JinyaHandler($level);
        $logger->pushHandler($handler);

        return $logger;
    }
}
