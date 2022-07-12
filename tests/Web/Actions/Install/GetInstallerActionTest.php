<?php

namespace Jinya\Tests\Web\Actions\Install;

use App\Web\Actions\Install\GetInstallerAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class GetInstallerActionTest extends TestCase
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
