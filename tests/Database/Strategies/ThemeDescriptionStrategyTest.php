<?php

namespace Jinya\Tests\Database\Strategies;

use App\Database\Strategies\ThemeDescriptionStrategy;
use PHPUnit\Framework\TestCase;

class ThemeDescriptionStrategyTest extends TestCase
{
    private ThemeDescriptionStrategy $jsonStrategy;

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
        $result = $this->jsonStrategy->hydrate('"key1":"value1"}', null);
        self::assertEquals(['en' => '"key1":"value1"}'], $result);
    }

    public function testExtract(): void
    {
        $obj = ['key1' => 'value1'];
        $result = $this->jsonStrategy->extract($obj);
        $this->assertIsString($result);
        $this->assertEquals(json_encode($obj, JSON_THROW_ON_ERROR), $result);
    }

    protected function setUp(): void
    {
        $this->jsonStrategy = new ThemeDescriptionStrategy();
    }
}
