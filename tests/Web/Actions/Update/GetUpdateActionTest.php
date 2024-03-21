<?php

namespace Jinya\Tests\Web\Actions\Update;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Update\GetUpdateAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetUpdateActionTest extends DatabaseAwareTestCase
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
