<?php

namespace Jinya\Cms\Locate;

use Jinya\Cms\Tests\DatabaseAwareTestCase;

class IpToLocationServiceTest extends DatabaseAwareTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $ipToLocationService = new IpToLocationService();
        $ipToLocationService->populateDatabase();
    }


    public function testLocateIp8_8_8_8(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('8.8.8.8');

        self::assertEquals('US', $result['country']);
        self::assertEquals('Mountain View', $result['city']);
    }

    public function testLocateIp2001_4860_4860__8888(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('2001:4860:4860::8888');

        self::assertEquals('US', $result['country']);
        self::assertEquals('Mountain View', $result['city']);
    }

    public function testLocateIp127_0_0_1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('127.0.0.1');

        self::assertEquals('ZZ', $result['country']);
        self::assertEquals('', $result['city']);
    }

    public function testLocateIp__1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('::1');

        self::assertEquals('ZZ', $result['country']);
        self::assertEquals('', $result['city']);
    }

    public function testLocateIp192_168_178_1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('192.168.178.1');

        self::assertEquals('ZZ', $result['country']);
        self::assertEquals('', $result['city']);
    }

    public function testLocateIp2001_db8_ffff_ffff_ffff_ffff_ffff_ffff(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('2001:db8:ffff:ffff:ffff:ffff:ffff:ffff');

        self::assertEquals('ZZ', $result['country']);
        self::assertEquals('', $result['city']);
    }

    public function testLocateIp292_168_178_1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('292.168.178.1');

        self::assertEquals('-1', $result['country']);
        self::assertEquals('-1', $result['city']);
    }

    public function testLocateIp2001_db8_ffff_ffff_ffff_ffff_ffff_fffg(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->locateIp('2001:db8:ffff:ffff:ffff:ffff:ffff:fffg');

        self::assertEquals('-1', $result['country']);
        self::assertEquals('-1', $result['city']);
    }

    public function testPopulateDatabase(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->populateDatabase();

        self::assertTrue($result);
    }
}
