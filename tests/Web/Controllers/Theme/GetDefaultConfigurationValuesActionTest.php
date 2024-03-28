<?php

namespace Jinya\Tests\Web\Controllers\Theme;

use App\Tests\ThemeActionTestCase;
use App\Web\Controllers\Theme\GetDefaultConfigurationValuesAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetDefaultConfigurationValuesActionTest extends ThemeActionTestCase
{
    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetDefaultConfigurationValuesAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->id]);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetDefaultConfigurationValuesAction();
        $action($request, $response, ['id' => -1]);
    }
}
