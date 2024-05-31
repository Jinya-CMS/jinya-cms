<?php

namespace Jinya\Cms\Configuration;

use Jinya\Configuration\Adapter\EnvironmentAdapter;
use Jinya\Configuration\Adapter\IniAdapter;
use Jinya\Configuration\Configuration;
use PDO;

use function Jinya\Database\configure_jinya_database;

class JinyaConfiguration extends Configuration
{
    private function __construct(bool $withDatabase = true)
    {
        $adapter = [
            new IniAdapter(__ROOT__ . '/jinya-configuration.ini'),
            new EnvironmentAdapter(),
        ];
        if ($withDatabase) {
            array_splice($adapter, 0, 0, new DatabaseConfigurationAdapter());
        }
        parent::__construct($adapter);
    }

    private static ?self $instance = null;

    public static function getConfiguration(bool $recreate = false): self
    {
        if ($recreate) {
            return self::$instance = new self();
        }

        return self::$instance ?? (self::$instance = new self());
    }

    public function reconfigureDatabase(): void
    {
        $configuration = new self(false);
        $database = $configuration->get('database', 'mysql', );
        $user = $configuration->get('user', 'mysql', ) ?: '';
        $password = $configuration->get('password', 'mysql', '');
        $host = $configuration->get('host', 'mysql', '127.0.0.1');
        $port = $configuration->get('port', 'mysql', 3306);

        $connectionString = "mysql:host=$host;port=$port;dbname=$database;user=$user;password=$password";
        $params = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        /** @phpstan-ignore argument.type */
        configure_jinya_database(__JINYA_CACHE, $connectionString, $params);
    }
}
