<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Tests\DatabaseAwareTestCase;

class LocateIpControllerTest extends DatabaseAwareTestCase
{
    public function testLocateIp(): void
    {
        $controller = new LocateIpController();
        $result = $controller->locateIp('185.216.179.123');
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('country', $body);
        self::assertArrayHasKey('region', $body);
        self::assertArrayHasKey('city', $body);

        self::assertEquals('Germany', $body['country']);
        self::assertEquals('Baden-Wurttemberg', $body['region']);
        self::assertEquals('Karlsruhe (Nordweststadt)', $body['city']);
    }
}
