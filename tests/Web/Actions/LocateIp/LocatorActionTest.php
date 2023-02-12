<?php

namespace Jinya\Tests\Web\Actions\LocateIp;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\LocateIp\LocatorAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class LocatorActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new LocatorAction();
        $result = $action($request, $response, ['ip' => '185.216.179.123']);
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
