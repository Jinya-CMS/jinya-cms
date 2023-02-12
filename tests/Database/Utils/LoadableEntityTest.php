<?php

namespace Jinya\Tests\Database\Utils;

use App\Database\Utils\LoadableEntity;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\AppSettingsInitializer;
use PDOException;

class LoadableEntityTest extends DatabaseAwareTestCase
{

    protected function setUp(): void
    {
        AppSettingsInitializer::loadDotEnv();
    }

    public function testExecuteSqlStringValidArray(): void
    {
        $result = LoadableEntity::executeSqlString('SELECT * FROM users');
        $this->assertIsArray($result);
    }

    public function testExecuteSqlStringValidInteger(): void
    {
        $result = LoadableEntity::executeSqlString('INSERT INTO configuration (id) VALUES(0)');
        $this->assertIsInt($result);
    }

    public function testExecuteSqlStringInvalid(): void
    {
        $this->expectException(PDOException::class);
        LoadableEntity::executeSqlString('SELECT FROM users');
    }

    public function testFetchColumnValidSql(): void
    {
        $result = LoadableEntity::fetchColumn('SELECT COUNT(*) FROM users');
        $this->assertIsInt($result);
    }

    public function testFetchColumnInvalidSql(): void
    {
        $this->expectException(PDOException::class);
        LoadableEntity::fetchColumn('SELECT COUNT(*) FORM users');
    }

    public function testGetPdo(): void
    {
        $pdo = LoadableEntity::getPdo();
        $this->assertNotNull($pdo);
    }
}
