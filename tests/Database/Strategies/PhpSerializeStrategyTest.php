<?php

namespace Database\Strategies;

use App\Database\Strategies\PhpSerializeStrategy;
use PHPUnit\Framework\TestCase;

class PhpSerializeStrategyTest extends TestCase
{

    private PhpSerializeStrategy $phpSerializeStrategy;

    protected function setUp(): void
    {
        $this->phpSerializeStrategy = new PhpSerializeStrategy();
    }

    public function testHydrateValidSerializeFormat(): void
    {
        $input = 'a:3:{i:0;s:16:"ROLE_SUPER_ADMIN";i:1;s:10:"ROLE_ADMIN";i:2;s:11:"ROLE_WRITER";}';
        $result = $this->phpSerializeStrategy->hydrate($input, null);
        $this->assertEquals(unserialize($input, ['allowed_classes' => []]), $result);
        $this->assertCount(3, $result);
    }

    public function testHydrateInvalidSerializeFormat(): void
    {
        $this->expectError();
        $this->phpSerializeStrategy->hydrate('a:3:{i:0;s:16:"ROLE_SUPER_ADMIN";i:1;s:10:"ROLE_ADMIN";i:2;s:1asd1:"ROLE_WRITER";}', null);
    }

    public function testExtract(): void
    {
        $obj = ['key1' => 'value1'];
        $result = $this->phpSerializeStrategy->extract($obj);
        $this->assertIsString($result);
        $this->assertEquals(serialize($obj), $result);
    }
}
