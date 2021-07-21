<?php

namespace App\Utils;

require_once __DIR__ . '/../../defines.php';

use Dotenv\Dotenv;

class AppSettingsInitializer
{
    public static function loadDotEnv(): void
    {
        if (file_exists(__ROOT__ . '/.env')) {
            $dotenv = Dotenv::createUnsafeImmutable(__ROOT__);
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