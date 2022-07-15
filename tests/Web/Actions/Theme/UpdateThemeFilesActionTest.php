<?php

namespace Jinya\Tests\Web\Actions\Theme;

use App\Database\Theme;
use App\Tests\ThemeActionTestCase;
use App\Web\Actions\Theme\CreateThemeAction;
use App\Web\Actions\Theme\UpdateThemeFilesAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use Symfony\Component\Filesystem\Filesystem;

class UpdateThemeFilesActionTest extends ThemeActionTestCase
{
    private string $name;

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $request = $request
            ->withBody(Stream::create(fopen(__ROOT__ . '/tests/files/unit-test-theme.zip', 'rb+')))
            ->withQueryParams(['name' => $this->name]);
        $response = new Response();
        $action = new CreateThemeAction();
        $result = $action($request, $response, []);
        self::assertEquals(204, $result->getStatusCode());

        $theme = Theme::findByName($this->name);
        $request = new ServerRequest('', '');
        $request = $request->withBody(Stream::create(fopen(__ROOT__ . '/tests/files/unit-test-theme.zip', 'rb+')));
        $response = new Response();
        $action = new UpdateThemeFilesAction();
        $result = $action($request, $response, ['id' => $theme->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdateThemeFilesAction();
        $action($request, $response, ['id' => -1]);
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
