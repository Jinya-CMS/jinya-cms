<?php

namespace Jinya\Tests\Web\Actions\Theme;

use App\Database\Menu;
use App\Database\ThemeMenu;
use App\Tests\ThemeActionTestCase;
use App\Web\Actions\Theme\GetLinkItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetLinkItemActionTest extends ThemeActionTestCase
{

    public function test__invokeGallery(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetLinkItemAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->getIdAsInt(), 'entityType' => 'gallery']);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeStandard(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $theme = $this->getDefaultTheme();
        $themeMenu = new ThemeMenu();
        $themeMenu->themeId = $theme->getIdAsInt();
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetLinkItemAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->getIdAsInt(), 'entityType' => 'menu']);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeSegmentPage(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetLinkItemAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->getIdAsInt(), 'entityType' => 'segment-page']);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeBlogCategory(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetLinkItemAction();
        $result = $action($request, $response, ['id' => $this->getDefaultTheme()->getIdAsInt(), 'entityType' => 'category']);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetLinkItemAction();
        $action($request, $response, ['id' => -1, 'entityType' => 'invalid']);
    }
}
