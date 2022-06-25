<?php

namespace Jinya\Tests\Database\Strategies;

use App\Database\Strategies\NullableBooleanStrategy;
use PHPUnit\Framework\TestCase;

class NullableBooleanStrategyTest extends TestCase
{

    public function testExtractFalse(): void
    {
        $strategy = new NullableBooleanStrategy();
        $result = $strategy->extract(false);
        $this->assertEquals(0, $result);
    }

    public function testExtractTrue(): void
    {
        $strategy = new NullableBooleanStrategy();
        $result = $strategy->extract(true);
        $this->assertEquals(1, $result);
    }

    public function testExtractNull(): void
    {
        $strategy = new NullableBooleanStrategy();
        $result = $strategy->extract(null);
        $this->assertNull($result);
    }

    public function testHydrateFalse(): void
    {
        $strategy = new NullableBooleanStrategy();
        $result = $strategy->hydrate(0);
        $this->assertFalse($result);
    }

    public function testHydrateTrue(): void
    {
        $strategy = new NullableBooleanStrategy();
        $result = $strategy->hydrate(1);
        $this->assertTrue($result);
    }

    public function testHydrateNull(): void
    {
        $strategy = new NullableBooleanStrategy();
        $result = $strategy->hydrate(null);
        $this->assertNull($result);
    }

    public function testHydrateBool(): void
    {
        $strategy = new NullableBooleanStrategy();
        $result = $strategy->hydrate(false);
        $this->assertFalse($result);
    }
}
