<?php

namespace Jinya\Tests\Maintenance\PhpInfo;

use App\Maintenance\PhpInfo\PhpExtension;
use PHPUnit\Framework\TestCase;

class PhpExtensionTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $extension = new PhpExtension();
        $extension->additionalData = [];
        $extension->extensionName = 'Test';
        $extension->iniValues = [];
        $extension->version = '1.0.0';

        self::assertEquals(['additionalData' => [], 'name' => 'Test', 'iniValues' => [], 'version' => '1.0.0'], $extension->jsonSerialize());
    }
}
