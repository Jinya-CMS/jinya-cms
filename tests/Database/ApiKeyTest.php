<?php

namespace Jinya\Tests\Database;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\LoadableEntity;
use App\Tests\DatabaseAwareTestCase;
use DateTime;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use RuntimeException;

class ApiKeyTest extends DatabaseAwareTestCase
{
    private ApiKey $testApiKey;
    private Artist $testArtist;

    protected function setUp(): void
    {
        $this->testArtist = new Artist();
        $this->testArtist->create();

        $this->testApiKey = new ApiKey();
        $this->testApiKey->userId = $this->testArtist->getIdAsInt();
        $this->testApiKey->setApiKey();
        $this->testApiKey->validSince = DateTime::createFromFormat(LoadableEntity::MYSQL_DATE_FORMAT, (new DateTime())->format(LoadableEntity::MYSQL_DATE_FORMAT)) ?: new DateTime();
        $this->testApiKey->userAgent = 'Firefox';
        $this->testApiKey->remoteAddress = '127.0.0.1';
        $this->testApiKey->create();
    }

    public function testFormat(): void
    {
        $expected = [
            'remoteAddress' => $this->testApiKey->remoteAddress,
            'validSince' => $this->testApiKey->validSince->format(DATE_ATOM),
            'userAgent' => $this->testApiKey->userAgent,
            'key' => $this->testApiKey->apiKey,
        ];

        $actual = $this->testApiKey->format();
        $this->assertEquals($expected, $actual);
    }

    public function testFormatInvalid(): void
    {
        $this->expectError();
        $apiKey = new ApiKey();
        $apiKey->userId = $this->testArtist->getIdAsInt();
        $apiKey->setApiKey();
        $apiKey->format();
    }

    public function testFindByApiKey(): void
    {
        $apiKey = ApiKey::findByApiKey($this->testApiKey->apiKey);
        $this->assertEquals($this->testApiKey->apiKey, $apiKey->apiKey);
        $this->assertEquals($this->testApiKey->validSince, $apiKey->validSince);
    }

    public function testFindByApiKeyNotExisting(): void
    {
        $apiKey = ApiKey::findByApiKey('nonexistingapikey');
        $this->assertNull($apiKey);
    }

    public function testCreate(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userId = $this->testArtist->getIdAsInt();
        $apiKey->setApiKey();
        $apiKey->validSince = new DateTime();
        $apiKey->userAgent = 'Firefox';
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->create();

        $savedApiKey = ApiKey::findByApiKey($apiKey->apiKey);
        $this->assertEquals($apiKey->apiKey, $savedApiKey->apiKey);
    }

    public function testCreateInvalidArtist(): void
    {
        $this->expectException(ForeignKeyFailedException::class);
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
        $apiKey->userId = $this->testArtist->getIdAsInt();
        $apiKey->setApiKey();
        $apiKey->create();
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        ApiKey::findAll();
    }

    public function testFindByArtist(): void
    {
        $apiKeys = ApiKey::findByArtist($this->testArtist->getIdAsInt());
        $this->assertGreaterThanOrEqual(1, iterator_count($apiKeys));
    }

    public function testFindByArtistNotExisting(): void
    {
        $apiKeys = ApiKey::findByArtist(-1);
        $this->assertEquals(0, iterator_count($apiKeys));
    }

    public function testDelete(): void
    {
        try {
            $this->testApiKey->delete();
            $this->assertTrue(true);
        } catch (ForeignKeyFailedException|UniqueFailedException|InvalidQueryException) {
            $this->fail();
        }
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
        $this->assertEquals($validSince->format(LoadableEntity::MYSQL_DATE_FORMAT), $apiKey->validSince->format(LoadableEntity::MYSQL_DATE_FORMAT));
    }

    public function testUpdateNotCreatedApiKey(): void
    {
        $this->expectError();
        $validSince = new DateTime();
        $testApiKey = new ApiKey();
        $testApiKey->validSince = $validSince;
        $testApiKey->update();
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        ApiKey::findById(0);
    }

    public function testGetArtist(): void
    {
        $artist = $this->testApiKey->getArtist();
        $this->assertNotNull($artist);
        $this->assertEquals($this->testArtist->id, $artist->id);
    }

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        ApiKey::findByKeyword('');
    }

    public function testSetApiKey(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userId = $this->testArtist->getIdAsInt();
        $apiKey->setApiKey();
        $this->assertStringStartsWith("jinya-api-token-$apiKey->userId", $apiKey->apiKey);
    }
}
