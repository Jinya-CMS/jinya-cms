<?php

namespace Jinya\Tests\Web\Actions\Version;

use App\Web\Actions\Version\GetVersionInfo;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class GetVersionInfoTest extends TestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetVersionInfo();
        $result = $action($request, $response, []);
        self::assertEquals(200, $result->getStatusCode());
    }
}
