<?php

namespace Jinya\Tests\Maintenance\PhpInfo;

use App\Maintenance\PhpInfo\IniValue;
use PHPUnit\Framework\TestCase;

class IniValueTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $iniValue = new IniValue();
        $iniValue->value = 'Test';
        $iniValue->configName = 'Test';
        self::assertEquals(['value' => 'Test', 'name' => 'Test'], $iniValue->jsonSerialize());
    }
}
