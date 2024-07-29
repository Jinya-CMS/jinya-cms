<?php

namespace Jinya\Cms\Console;

use PHPUnit\Framework\TestCase;

class JinyaCommandTest extends TestCase
{
    public function test__construct(): void
    {
        $attribute = new JinyaCommand('Test');
        self::assertEquals('Test', $attribute->command);
    }
}
