<?php

namespace Jinya\Tests\Web\Actions\Update;

use App\Web\Actions\Update\GetUpdateAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class GetUpdateActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetUpdateAction();
        $result = $action($request, $response, []);
        self::assertEquals(200, $result->getStatusCode());
    }
}
