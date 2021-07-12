<?php

namespace Database\Strategies;

use App\Database\Strategies\JsonStrategy;
use JsonException;
use PHPUnit\Framework\TestCase;

class JsonStrategyTest extends TestCase
{

    private JsonStrategy $jsonStrategy;

    protected function setUp(): void
    {
        $this->jsonStrategy = new JsonStrategy();
    }

    public function testHydrateValidJson(): void
    {
        $json = '{"key1":"value1"}';
        $result = $this->jsonStrategy->hydrate($json, null);
        $this->assertEquals(json_decode($json, true, 512, JSON_THROW_ON_ERROR), $result);
        $this->assertArrayHasKey('key1', $result);
        $this->assertEquals('value1', $result['key1']);
    }

    public function testHydrateInvalidJson(): void
    {
        $this->expectException(JsonException::class);
        $this->jsonStrategy->hydrate('"key1":"value1"}', null);
    }

    public function testExtract(): void
    {
        $obj = ['key1' => 'value1'];
        $result = $this->jsonStrategy->extract($obj);
        $this->assertIsString($result);
        $this->assertEquals(json_encode($obj, JSON_THROW_ON_ERROR), $result);
    }
}
