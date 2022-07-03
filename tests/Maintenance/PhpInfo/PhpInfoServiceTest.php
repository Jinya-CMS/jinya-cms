<?php

namespace Jinya\Tests\Maintenance\PhpInfo;

use App\Maintenance\PhpInfo\PhpInfoService;
use PHPUnit\Framework\TestCase;

class PhpInfoServiceTest extends TestCase
{

    private PhpInfoService $infoService;

    public function testGetUname(): void
    {
        self::assertEquals(php_uname(), $this->infoService->getUname());
    }

    public function testGetLoadedExtensions(): void
    {
        $loadedExtensions = $this->infoService->getLoadedExtensions();
        self::assertNotEmpty($loadedExtensions);
        self::assertCount(count(get_loaded_extensions()), $loadedExtensions);
    }

    public function testGetZendVersion(): void
    {
        self::assertEquals(zend_version(), $this->infoService->getZendVersion());
    }

    public function testGetVersion(): void
    {
        self::assertEquals(PHP_VERSION, $this->infoService->getVersion());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->infoService = new PhpInfoService();
    }
}
