<?php


require __DIR__ . '/defines.php';
require __DIR__ . '/vendor/autoload.php';

\App\Utils\AppSettingsInitializer::loadDotEnv();

$database = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER') ?: '';
$password = getenv('MYSQL_PASSWORD') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: 3306;

\Jinya\Database\configure_jinya_database(
    __DIR__ . '/var/cache/jinya',
    "mysql:host=$host;port=$port;dbname=$database;user=$user;password=$password",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);
