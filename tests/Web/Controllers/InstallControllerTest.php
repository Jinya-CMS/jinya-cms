<?php

namespace Jinya\Tests\Web\Controllers;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\InstallController;
use Jinya\Database\Cache\KeyCache;
use Nyholm\Psr7\ServerRequest;
use PDO;

use function Jinya\Database\configure_jinya_database;

class InstallControllerTest extends DatabaseAwareTestCase
{
    private function getController(array $body): InstallController
    {
        foreach ($body as $key => $item) {
            $_POST[$key] = $item;
        }

        $controller = new InstallController();
        $controller->request = new ServerRequest('', '');
        $controller->request = $controller->request->withParsedBody($body);
        $controller->body = $body;

        return $controller;
    }

    public function testGetInstall(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getInstall();
        self::assertEquals(200, $result->getStatusCode());
    }

    public function testPostInstall(): void
    {
        try {
            $controller = $this->getController(include __DIR__ . '/../../files/.env.php');
            $result = $controller->postInstall();

            self::assertEquals(200, $result->getStatusCode());

            $controller = $this->getController([
                'email' => 'test@example.com',
                'password' => 'start1234',
                'artistname' => 'Theo Test',
                'action' => true,
            ]);
            $result = $controller->postInstall();

            self::assertEquals(301, $result->getStatusCode());
            self::assertEquals('/designer', $result->getHeaderLine('Location'));
        } finally {
            @unlink(__ROOT__ . '/.env');
        }
    }

    public function testPostInstallFirstAdminNoDotEnv(): void
    {
        KeyCache::unset('___Config', 'CacheDirectory');
        KeyCache::unset('___Config', 'ConnectionString');
        KeyCache::unset('___Config', 'ConnectionOptions');
        KeyCache::unset('___Config', 'EnableAutoConvert');
        KeyCache::unset('___Database', 'PDO');

        configure_jinya_database(
            __JINYA_CACHE,
            "mysql:host=host;port=0;dbname=database;user=user;password=password",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        try {
            $controller = $this->getController([
                'email' => 'test@example.com',
                'password' => 'start1234',
                'artistname' => 'Theo Test',
                'action' => true,
            ]);
            $result = $controller->postInstall();
            self::assertEquals(200, $result->getStatusCode());
        } finally {
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
        }
    }
}
