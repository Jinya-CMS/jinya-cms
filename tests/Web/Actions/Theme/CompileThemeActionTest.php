<?php

namespace Jinya\Tests\Web\Actions\Theme;

use App\Tests\ThemeActionTestCase;
use App\Web\Actions\Theme\CompileThemeAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CompileThemeActionTest extends ThemeActionTestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new CompileThemeAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new CompileThemeAction();
        $action($request, $response, ['id' => -1]);
    }
}
