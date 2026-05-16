<?php

namespace Jinya\Cms\Logging;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;

class JinyaHandler extends AbstractHandler
{
    private readonly StreamHandler $streamHandler;

    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        global $__RUNNING_IN_DOCKER;
        parent::__construct($level, $bubble);
        $path = $__RUNNING_IN_DOCKER ? 'php://stdout' : __JINYA_LOGS . '/app.log';
        $this->streamHandler = new StreamHandler($path, $level);
    }

    public function handle(LogRecord $record): bool
    {
        if (extension_loaded('frankenphp')) {
            $frankenlevel = match ($record->level) {
                Level::Debug => FRANKENPHP_LOG_LEVEL_DEBUG,
                Level::Info => FRANKENPHP_LOG_LEVEL_INFO,
                Level::Notice => FRANKENPHP_LOG_LEVEL_INFO + 1,
                Level::Warning => FRANKENPHP_LOG_LEVEL_WARN,
                Level::Error => FRANKENPHP_LOG_LEVEL_ERROR,
                Level::Critical => FRANKENPHP_LOG_LEVEL_ERROR + 1,
                Level::Alert => FRANKENPHP_LOG_LEVEL_ERROR + 1 + 1,
                Level::Emergency => FRANKENPHP_LOG_LEVEL_ERROR + 1 + 1 + 1,
            };
            frankenphp_log($record->message, $frankenlevel, $record->context);
        } else {
            $this->streamHandler->handle($record);
        }
    }
}
