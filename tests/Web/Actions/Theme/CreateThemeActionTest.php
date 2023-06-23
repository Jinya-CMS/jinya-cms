<?php

namespace Jinya\Tests\Web\Actions\Theme;

use App\Tests\ThemeActionTestCase;
use App\Web\Actions\Theme\CreateThemeAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use Symfony\Component\Filesystem\Filesystem;

class CreateThemeActionTest extends ThemeActionTestCase
{
    private string $name;

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $request = $request
            /** @phpstan-ignore-next-line */
            ->withBody(Stream::create(fopen(__ROOT__ . '/tests/files/unit-test-theme.zip', 'rb+')))
            ->withQueryParams(['name' => $this->name]);
        $response = new Response();
        $action = new CreateThemeAction();
        $result = $action($request, $response, []);
        self::assertEquals(204, $result->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->name = Uuid::uuid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $fs = new Filesystem();
        $fs->remove(__ROOT__ . '/themes/' . $this->name);
    }
}
