<?php

namespace Jinya\Tests\Web\Actions\Install;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Install\GetInstallerAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetInstallerActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetInstallerAction();
        $result = $action($request, $response, []);
        self::assertEquals(200, $result->getStatusCode());
    }
}
