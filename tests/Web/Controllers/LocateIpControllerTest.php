<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Tests\DatabaseAwareTestCase;

class LocateIpControllerTest extends DatabaseAwareTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function testLocateIp(): void
    {
        $controller = new LocateIpController();
        $result = $controller->locateIp('8.8.8.8');
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('country', $body);
        self::assertArrayHasKey('city', $body);


        self::assertEquals('DE', $body['country']);
        self::assertEquals('Nuremberg', $body['city']);
    }
}
