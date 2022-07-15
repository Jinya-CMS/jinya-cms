<?php

namespace Jinya\Tests\Web\Actions\Theme;

use App\Tests\ThemeActionTestCase;
use App\Web\Actions\Theme\PutConfigurationAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class PutConfigurationActionTest extends ThemeActionTestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['configuration' => ['test' => Uuid::uuid()]]);
        $response = new Response();
        $action = new PutConfigurationAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new PutConfigurationAction();
        $action($request, $response, ['id' => -1]);
    }
}
