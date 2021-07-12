<?php

namespace Database\Utils;

use App\Database\ApiKey;
use App\Database\Utils\LoadableEntity;
use App\Utils\AppSettingsInitializer;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use PDOException;
use PHPUnit\Framework\TestCase;
use TypeError;

class LoadableEntityTest extends TestCase
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
        $result = LoadableEntity::executeSqlString('UPDATE configuration SET messaging_center_enabled = 0');
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

    public function testHydrateSingleResultEmptyData(): void
    {
        $apiKey = LoadableEntity::hydrateSingleResult([], new ApiKey());
        $this->assertNotNull($apiKey);
    }

    public function testHydrateSingleResultValidFilledData(): void
    {
        $apiKey = LoadableEntity::hydrateSingleResult([
            'api_key' => 'testapikey',
            'user_id' => 4,
            'valid_since' => '2020-02-07 20:03:09',
            'user_agent' => 'Firefox',
            'remote_address' => '127.0.0.1',
        ], new ApiKey(), ['validSince' => new DateTimeFormatterStrategy(LoadableEntity::MYSQL_DATE_FORMAT)]);
        $this->assertNotNull($apiKey);
    }

    public function testHydrateSingleResultInvalidFilledData(): void
    {
        $this->expectException(TypeError::class);
        LoadableEntity::hydrateSingleResult([
            'api_key' => 3,
            'user_id' => 'Invalid value',
            'valid_since' => 'No valid date',
            'user_agent' => 'Firefox',
            'remote_address' => '127.0.0.1',
        ], new ApiKey(), ['validSince' => new DateTimeFormatterStrategy(LoadableEntity::MYSQL_DATE_FORMAT)]);
    }

    public function testHydrateMultipleResultEmptyData(): void
    {
        $apiKeys = LoadableEntity::hydrateMultipleResults([], new ApiKey());
        $this->assertNotNull($apiKeys);
        $apiKeys->current();
    }

    public function testHydrateMultipleResultValidFilledData(): void
    {
        $apiKeys = LoadableEntity::hydrateMultipleResults([[
            'api_key' => 'testapikey',
            'user_id' => 4,
            'valid_since' => '2020-02-07 20:03:09',
            'user_agent' => 'Firefox',
            'remote_address' => '127.0.0.1',
        ]], new ApiKey(), ['validSince' => new DateTimeFormatterStrategy(LoadableEntity::MYSQL_DATE_FORMAT)]);
        $this->assertNotNull($apiKeys);
        foreach ($apiKeys as $apiKey) {
            $this->assertNotNull($apiKey);
        }
    }

    public function testHydrateMultipleResultInvalidFilledData(): void
    {
        $this->expectException(TypeError::class);
        $apiKeys = LoadableEntity::hydrateMultipleResults([[
            'api_key' => 3,
            'user_id' => 'Invalid value',
            'valid_since' => 'No valid date',
            'user_agent' => 'Firefox',
            'remote_address' => '127.0.0.1',
        ]], new ApiKey(), ['validSince' => new DateTimeFormatterStrategy(LoadableEntity::MYSQL_DATE_FORMAT)]);
        $apiKeys->current();
        $this->fail();
    }
}
