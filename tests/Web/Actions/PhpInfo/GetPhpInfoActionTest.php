<?php

namespace Jinya\Tests\Web\Actions\PhpInfo;

use App\Web\Actions\PhpInfo\GetPhpInfoAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class GetPhpInfoActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetPhpInfoAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(200, $result->getStatusCode());
        self::assertArrayHasKey('apache', $body);
        self::assertArrayHasKey('system', $body);
        self::assertArrayHasKey('php', $body);
        self::assertArrayHasKey('zend', $body);
    }
}
