<?php

namespace Jinya\Tests\Web\Controllers\Theme;

use App\Tests\ThemeActionTestCase;
use App\Web\Controllers\Theme\PutStyleVariablesAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class PutStyleVariablesActionTest extends ThemeActionTestCase
{
    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['variables' => ['test' => Uuid::uuid()]]);
        $response = new Response();
        $action = new PutStyleVariablesAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->id]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new PutStyleVariablesAction();
        $action($request, $response, ['id' => -1]);
    }
}
