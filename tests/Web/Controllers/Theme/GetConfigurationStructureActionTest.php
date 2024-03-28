<?php

namespace Jinya\Tests\Web\Controllers\Theme;

use App\Tests\ThemeActionTestCase;
use App\Web\Controllers\Theme\GetConfigurationStructureAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetConfigurationStructureActionTest extends ThemeActionTestCase
{
    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetConfigurationStructureAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->id]);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetConfigurationStructureAction();
        $action($request, $response, ['id' => -1]);
    }
}
