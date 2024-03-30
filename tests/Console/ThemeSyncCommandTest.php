<?php

namespace Jinya\Cms\Console;

use Jinya\Cms\Console\ThemeSyncCommand;
use Jinya\Cms\Tests\DatabaseAwareTestCase;

class ThemeSyncCommandTest extends DatabaseAwareTestCase
{
    public function testRun(): void
    {
        $command = new ThemeSyncCommand();
        $command->run();
        self::assertTrue(true);
    }
}
