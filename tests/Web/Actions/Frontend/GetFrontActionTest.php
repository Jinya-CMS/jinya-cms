<?php

namespace Jinya\Tests\Web\Actions\Frontend;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\MenuItem;
use App\Database\SegmentPage;
use App\Database\SimplePage;
use App\Tests\FrontTestCase;
use App\Web\Actions\Frontend\GetFrontAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetFrontActionTest extends FrontTestCase
{

    public function test__invokeHome(): void
    {
        $_SERVER['REQUEST_URI'] = '/';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => '']);

        self::assertEquals(200, $result->getStatusCode());

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => '/']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(404, $result->getStatusCode());
    }

    public function test__invokeSegmentPage(): void
    {
        $page = new SegmentPage();
        $page->name = Uuid::uuid();
        $page->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->segmentPageId = $page->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeSimplePage(): void
    {
        $page = new SimplePage();
        $page->title = Uuid::uuid();
        $page->content = '';
        $page->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->pageId = $page->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeForm(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->formId = $form->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeGallery(): void
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->galleryId = $gallery->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeArtist(): void
    {
        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->artistId = CurrentUser::$currentUser->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeBlogCategory(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->categoryId = $category->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeBlogHomePage(): void
    {
        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->blogHomePage = true;
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeMenuItem404(): void
    {
        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(404, $result->getStatusCode());
    }

    public function test__invokeBlogPost(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->public = true;
        $post->slug = Uuid::uuid();
        $post->create();

        $_SERVER['REQUEST_URI'] = '/' . $post->slug;
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetFrontAction();
        $result = $action($request, $response, ['slug' => $post->slug]);

        self::assertEquals(200, $result->getStatusCode());
    }
}
