<?php

namespace Jinya\Tests\Database;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Tests\DatabaseAwareTestCase;
use DateTime;
use Jinya\Database\Exception\ForeignKeyFailedException;
use PDOException;

class ApiKeyTest extends DatabaseAwareTestCase
{
    private ApiKey $testApiKey;
    private Artist $testArtist;

    public function testFormat(): void
    {
        $expected = [
            'remoteAddress' => $this->testApiKey->remoteAddress,
            'validSince' => $this->testApiKey->validSince->format(DATE_ATOM),
            'userAgent' => $this->testApiKey->userAgent,
            'key' => $this->testApiKey->apiKey,
        ];

        $actual = $this->testApiKey->format();
        self::assertEquals($expected, $actual);
    }

    public function testFormatInvalid(): void
    {
        $this->expectError();
        $apiKey = new ApiKey();
        $apiKey->userId = $this->testArtist->id;
        $apiKey->setApiKey();
        $apiKey->format();
    }

    public function testFindByApiKey(): void
    {
        $apiKey = ApiKey::findByApiKey($this->testApiKey->apiKey);
        self::assertEquals($this->testApiKey->apiKey, $apiKey->apiKey);
        self::assertEquals($this->testApiKey->validSince, $apiKey->validSince);
    }

    public function testFindByApiKeyNotExisting(): void
    {
        $apiKey = ApiKey::findByApiKey('nonexistingapikey');
        self::assertNull($apiKey);
    }

    public function testCreate(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userId = $this->testArtist->id;
        $apiKey->setApiKey();
        $apiKey->validSince = new DateTime();
        $apiKey->userAgent = 'Firefox';
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->create();

        $savedApiKey = ApiKey::findByApiKey($apiKey->apiKey);
        self::assertEquals($apiKey->apiKey, $savedApiKey->apiKey);
    }

    public function testCreateInvalidArtist(): void
    {
        $this->expectException(PDOException::class);
        $apiKey = new ApiKey();
        $apiKey->userId = -1;
        $apiKey->setApiKey();
        $apiKey->validSince = new DateTime();
        $apiKey->userAgent = 'Firefox';
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->create();
    }

    public function testCreateMissingFields(): void
    {
        $this->expectError();
        $apiKey = new ApiKey();
        $apiKey->userId = $this->testArtist->id;
        $apiKey->setApiKey();
        $apiKey->create();
    }

    public function testFindByArtist(): void
    {
        $apiKeys = ApiKey::findByArtist($this->testArtist->id);
        self::assertGreaterThanOrEqual(1, iterator_count($apiKeys));
    }

    public function testFindByArtistNotExisting(): void
    {
        $apiKeys = ApiKey::findByArtist(-1);
        self::assertEquals(0, iterator_count($apiKeys));
    }

    public function testDelete(): void
    {
        $this->testApiKey->delete();
        self::assertTrue(true);
    }

    public function testDeleteNotCreatedApiKey(): void
    {
        $this->expectError();
        $testApiKey = new ApiKey();
        $testApiKey->delete();
    }

    public function testUpdate(): void
    {
        $validSince = new DateTime();
        $this->testApiKey->validSince = $validSince;
        $this->testApiKey->update();
        $apiKey = ApiKey::findByApiKey($this->testApiKey->apiKey);
        self::assertEquals(
            $validSince->format(MYSQL_DATE_FORMAT),
            $apiKey->validSince->format(MYSQL_DATE_FORMAT)
        );
    }

    public function testUpdateNotCreatedApiKey(): void
    {
        $this->expectError();
        $validSince = new DateTime();
        $testApiKey = new ApiKey();
        $testApiKey->validSince = $validSince;
        $testApiKey->update();
    }

    public function testGetArtist(): void
    {
        $artist = $this->testApiKey->getArtist();
        self::assertNotNull($artist);
        self::assertEquals($this->testArtist->id, $artist->id);
    }

    public function testSetApiKey(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userId = $this->testArtist->id;
        $apiKey->setApiKey();
        self::assertStringStartsWith("jinya-api-token-$apiKey->userId", $apiKey->apiKey);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->testArtist = new Artist();
        $this->testArtist->create();

        $this->testApiKey = new ApiKey();
        $this->testApiKey->userId = $this->testArtist->id;
        $this->testApiKey->setApiKey();
        $this->testApiKey->validSince = DateTime::createFromFormat(
            MYSQL_DATE_FORMAT,
            (new DateTime())->format(MYSQL_DATE_FORMAT)
        ) ?: new DateTime();
        $this->testApiKey->userAgent = 'Firefox';
        $this->testApiKey->remoteAddress = '127.0.0.1';
        $this->testApiKey->create();
    }
}
