<?php

namespace Jinya\Tests\Web\Controllers\Theme;

use App\Database\BlogCategory;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\ModernPage;
use App\Database\ThemeBlogCategory;
use App\Tests\ThemeActionTestCase;
use App\Web\Controllers\Theme\PutLinkItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class PutLinkItemActionTest extends ThemeActionTestCase
{
    public function test__invokeGallery(): void
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['gallery' => $gallery->id]);
        $response = new Response();
        $action = new PutLinkItemAction();
        $result = $action(
            $request,
            $response,
            ['id' => $this->getDefaultTheme()->id, 'entityType' => 'gallery', 'name' => 'test']
        );
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeStandard(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['menu' => $menu->id]);
        $response = new Response();
        $action = new PutLinkItemAction();
        $result = $action(
            $request,
            $response,
            ['id' => $this->getDefaultTheme()->id, 'entityType' => 'menu', 'name' => 'test']
        );
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeSegmentPage(): void
    {
        $segmentPage = new ModernPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['segmentPage' => $segmentPage->id]);
        $response = new Response();
        $action = new PutLinkItemAction();
        $result = $action(
            $request,
            $response,
            ['id' => $this->getDefaultTheme()->id, 'entityType' => 'segment-page', 'name' => 'test']
        );
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeBlogCategory(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['blogCategory' => $category->id]);
        $response = new Response();
        $action = new PutLinkItemAction();
        $result = $action(
            $request,
            $response,
            ['id' => $this->getDefaultTheme()->id, 'entityType' => 'category', 'name' => 'test']
        );
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeBlogCategoryUpdate(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $theme = $this->getDefaultTheme();

        $themeCategory = new ThemeBlogCategory();
        $themeCategory->name = 'test';
        $themeCategory->themeId = $theme->id;
        $themeCategory->blogCategoryId = $category->id;
        $themeCategory->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['blogCategory' => $category->id]);
        $response = new Response();
        $action = new PutLinkItemAction();
        $result = $action(
            $request,
            $response,
            ['id' => $this->getDefaultTheme()->id, 'entityType' => 'category', 'name' => 'test']
        );
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Theme not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new PutLinkItemAction();
        $action($request, $response, ['id' => -1, 'entityType' => 'invalid', 'name' => 'invalid']);
    }
}
