<?php


use Dotenv\Dotenv;

use function Jinya\Database\configure_jinya_database;

require __DIR__ . '/defines.php';
require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env') || file_exists(__DIR__ . '/.env.dist')) {
    $dotenv = Dotenv::createUnsafeImmutable(__DIR__, ['.env', '.env.dist']);
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

$database = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER') ?: '';
$password = getenv('MYSQL_PASSWORD') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: 3306;

configure_jinya_database(
    __JINYA_CACHE,
    "mysql:host=$host;port=$port;dbname=$database;user=$user;password=$password",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);
