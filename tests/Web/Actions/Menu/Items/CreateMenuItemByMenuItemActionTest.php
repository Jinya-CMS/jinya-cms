<?php

namespace Jinya\Tests\Web\Actions\Menu\Items;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\MenuItem;
use App\Database\ModernPage;
use App\Database\ClassicPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Menu\Items\CreateMenuItemByMenuItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CreateMenuItemByMenuItemActionTest extends DatabaseAwareTestCase
{
    private Menu $menu;

    public function test__invokeWithArtist(): void
    {
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'artist' => CurrentUser::$currentUser->getIdAsInt(), 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeWithArtistNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Artist not found');
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'artist' => -1, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
    }

    public function test__invokeWithForm(): void
    {
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'form' => $form->getIdAsInt(), 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeWithFormNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Form not found');
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'form' => -1, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
    }

    public function test__invokeWithPage(): void
    {
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $page = new ClassicPage();
        $page->title = Uuid::uuid();
        $page->content = Uuid::uuid();
        $page->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'page' => $page->getIdAsInt(), 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeWithPageNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Page not found');
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'page' => -1, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
    }

    public function test__invokeWithSegmentPage(): void
    {
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $segmentPage = new ModernPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'segmentPage' => $segmentPage->getIdAsInt(), 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeWithSegmentPageNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Segment page not found');
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'segmentPage' => -1, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
    }

    public function test__invokeWithGallery(): void
    {
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'gallery' => $gallery->getIdAsInt(), 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeWithGalleryNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Gallery not found');
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'gallery' => -1, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
    }

    public function test__invokeWithCategory(): void
    {
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'category' => $category->getIdAsInt(), 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeWithCategoryNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Category not found');
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'category' => -1, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
    }

    public function test__invokeWithBlogHomePage(): void
    {
        $menu = new MenuItem();
        $menu->title = Uuid::uuid();
        $menu->position = 0;
        $menu->menuId = $this->menu->getIdAsInt();
        $menu->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'blogHomePage' => true, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menu->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeMenuItemNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Menu item not found');
        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['route' => 'test', 'blogHomePage' => true, 'title' => Uuid::uuid(), 'position' => 0, 'highlighted' => true]);
        $response = new Response();
        $action = new CreateMenuItemByMenuItemAction();
        $action($request, $response, ['menuItemId' => -1]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->menu = new Menu();
        $this->menu->name = Uuid::uuid();
        $this->menu->create();
    }
}
