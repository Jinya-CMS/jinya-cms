<?php

namespace Jinya\Cms\Theming;

use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    public function testGetPlatesEngine(): void
    {
        $engine = Engine::getPlatesEngine();

        self::assertArrayHasKey('mailing', $engine->getFolders());
        self::assertArrayHasKey('emergency', $engine->getFolders());

        self::assertEquals(__ROOT__ . '/src/Mailing/Templates', $engine->getFolders()['mailing']->path);
        self::assertEquals(__ROOT__ . '/src/Emergency/Templates', $engine->getFolders()['emergency']->path);
    }
}
