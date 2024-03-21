<?php

namespace App\Utils;

require_once __DIR__ . '/../../defines.php';

use Dotenv\Dotenv;

/**
 * Initializes all environment variables from .env
 */
abstract class AppSettingsInitializer
{
    /**
     * Loads the local .env file and validates all environment variables
     *
     * @return void
     */
    public static function loadDotEnv(): void
    {
        if (file_exists(__ROOT__ . '/.env') || file_exists(__ROOT__ . '/.env.dist')) {
            $dotenv = Dotenv::createUnsafeImmutable(__ROOT__, ['.env', '.env.dist']);
            $dotenv->load();
            $dotenv->required(
                [
                    'APP_ENV',
                    'MYSQL_PASSWORD',
                    'MYSQL_USER',
                    'MYSQL_DATABASE',
                    'MYSQL_HOST',
                    'MAILER_HOST',
                    'MAILER_PORT',
                    'MAILER_SMTP_AUTH',
                    'MAILER_FROM',
                ]
            );
        }
    }
}
