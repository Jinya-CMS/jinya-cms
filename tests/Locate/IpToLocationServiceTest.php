<?php

namespace Jinya\Cms\Locate;

use Jinya\Cms\Tests\DatabaseAwareTestCase;

class IpToLocationServiceTest extends DatabaseAwareTestCase
{
    public function testLocateIp152_53_13_49(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('152.53.13.49');

        self::assertEquals('DE', $countryCode);
    }

    public function testLocateIp2a03_4000_6b_144_2858_ff_febe_765d(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('2a03:4000:6b:144:2858:ff:febe:765d');

        self::assertEquals('DE', $countryCode);
    }

    public function testLocateIp127_0_0_1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('127.0.0.1');

        self::assertEquals('ZZ', $countryCode);
    }

    public function testLocateIp__1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('::1');

        self::assertEquals('ZZ', $countryCode);
    }

    public function testLocateIp192_168_178_1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('192.168.178.1');

        self::assertEquals('ZZ', $countryCode);
    }

    public function testLocateIp2001_db8_ffff_ffff_ffff_ffff_ffff_ffff(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('2001:db8:ffff:ffff:ffff:ffff:ffff:ffff');

        self::assertEquals('ZZ', $countryCode);
    }

    public function testLocateIp292_168_178_1(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('292.168.178.1');

        self::assertEquals('-1', $countryCode);
    }

    public function testLocateIp2001_db8_ffff_ffff_ffff_ffff_ffff_fffg(): void
    {
        $ipToLocationService = new IpToLocationService();
        $countryCode = $ipToLocationService->locateIp('2001:db8:ffff:ffff:ffff:ffff:ffff:fffg');

        self::assertEquals('-1', $countryCode);
    }

    public function testPopulateDatabase(): void
    {
        $ipToLocationService = new IpToLocationService();
        $result = $ipToLocationService->populateDatabase();

        self::assertTrue($result);
    }
}
