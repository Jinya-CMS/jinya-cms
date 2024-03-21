<?php

namespace Jinya\Tests\Console;

use App\Console\ThemeSyncCommand;
use App\Tests\DatabaseAwareTestCase;

class ThemeSyncCommandTest extends DatabaseAwareTestCase
{
    public function testRun(): void
    {
        $command = new ThemeSyncCommand();
        $command->run();
        self::assertTrue(true);
    }
}
