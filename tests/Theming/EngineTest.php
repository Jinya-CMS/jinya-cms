<?php

namespace Jinya\Tests\Theming;

use App\Theming\Engine;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    public function testGetPlatesEngine(): void
    {
        $engine = Engine::getPlatesEngine();
        self::assertTrue($engine->getFolders()->exists('mailing'));
        self::assertTrue($engine->getFolders()->exists('emergency'));
        self::assertEquals(__ROOT__ . '/src/Mailing/Templates', $engine->getFolders()->get('mailing')->getPath());
        self::assertEquals(__ROOT__ . '/src/Emergency/Templates', $engine->getFolders()->get('emergency')->getPath());
    }
}
