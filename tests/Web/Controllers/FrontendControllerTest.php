<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\BlogCategory;
use Jinya\Cms\Database\BlogPost;
use Jinya\Cms\Database\ClassicPage;
use Jinya\Cms\Database\Form;
use Jinya\Cms\Database\Gallery;
use Jinya\Cms\Database\ModernPage;
use Jinya\Cms\Tests\FrontTestCase;
use Jinya\Cms\Web\Controllers\FrontendController;
use Faker\Provider\Uuid;
use Nyholm\Psr7\ServerRequest;

class FrontendControllerTest extends FrontTestCase
{
    private function getController(bool $asJson = false): FrontendController
    {
        $controller = new FrontendController();
        if ($asJson) {
            $controller->request = (new ServerRequest('', '', ['Accept' => 'application/json']));
        } else {
            $controller->request = new ServerRequest('', '');
        }

        return $controller;
    }

    public function testGetBlogFront(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->public = true;
        $post->slug = Uuid::uuid();
        $post->create();

        $controller = $this->getController();
        $result = $controller->blogFrontend($post->slug);
        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals('text/html', $result->getHeaderLine('Content-Type'));

        $controller = $this->getController(true);
        $result = $controller->blogFrontend($post->slug);
        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals('application/json', $result->getHeaderLine('Content-Type'));
    }

    public function testGetBlogFrontPostNotFound(): void
    {
        $controller = $this->getController();
        $result = $controller->blogFrontend(Uuid::uuid());
        self::assertEquals(404, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->blogFrontend(Uuid::uuid());
        self::assertEquals(404, $result->getStatusCode());
    }

    public function testGetFrontHome(): void
    {
        $controller = $this->getController();
        $result = $controller->frontend('');

        self::assertEquals(200, $result->getStatusCode());

        $result = $controller->frontend('/');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('');

        self::assertEquals(200, $result->getStatusCode());

        $result = $controller->frontend('/');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontNotFound(): void
    {
        $controller = $this->getController();
        $result = $controller->frontend('/test');

        self::assertEquals(404, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('/test');

        self::assertEquals(404, $result->getStatusCode());
    }

    public function testGetFrontModernPage(): void
    {
        $page = new ModernPage();
        $page->name = Uuid::uuid();
        $page->create();

        $menuItem = [
            'menuId' => $this->menu->id,
            'modernPageId' => $page->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontClassicPage(): void
    {
        $page = new ClassicPage();
        $page->title = Uuid::uuid();
        $page->content = '';
        $page->create();

        $menuItem = [
            'menuId' => $this->menu->id,
            'classicPageId' => $page->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontForm(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $menuItem = [
            'menuId' => $this->menu->id,
            'formId' => $form->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontGallery(): void
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $menuItem = [
            'menuId' => $this->menu->id,
            'galleryId' => $gallery->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontArtist(): void
    {
        $menuItem = [
            'menuId' => $this->menu->id,
            'artistId' => CurrentUser::$currentUser->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontBlogCategory(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $menuItem = [
            'menuId' => $this->menu->id,
            'categoryId' => $category->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontBlogHomePage(): void
    {
        $menuItem = [
            'menuId' => $this->menu->id,
            'blogHomePage' => true,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testGetFrontMenuItem404(): void
    {
        $menuItem = [
            'menuId' => $this->menu->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->frontend('test');

        self::assertEquals(404, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend('test');

        self::assertEquals(404, $result->getStatusCode());
    }

    public function testGetFrontBlogPost(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->public = true;
        $post->slug = Uuid::uuid();
        $post->create();

        $controller = $this->getController();
        $result = $controller->frontend($post->slug);

        self::assertEquals(200, $result->getStatusCode());

        $controller = $this->getController(true);
        $result = $controller->frontend($post->slug);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function testPostFront(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $form->replaceItems([
            [
                'formId' => $form->id,
                'label' => Uuid::uuid(),
                'isFromAddress' => true,
                'isSubject' => true,
                'type' => 'text',
            ]
        ]);
        $menuItem = [
            'menuId' => $this->menu->id,
            'formId' => $form->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $items = $form->getItems();
        foreach ($items as $item) {
            $_POST[(string)$item->id] = 'test@example.com';
        }

        $controller = $this->getController();
        $result = $controller->postForm('test');

        self::assertEquals(302, $result->getStatusCode());
    }

    public function testPostFrontMissingFields(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $form->replaceItems([
            [
                'formId' => $form->id,
                'label' => Uuid::uuid(),
                'isFromAddress' => true,
                'isSubject' => true,
                'isRequired' => true,
                'type' => 'text',
            ]
        ]);
        $menuItem = [
            'menuId' => $this->menu->id,
            'formId' => $form->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $_POST = [];
        $controller = $this->getController();
        $result = $controller->postForm('test');

        self::assertEquals(400, $result->getStatusCode());
    }

    public function testPostFrontNotFound(): void
    {
        $menuItem = [
            'menuId' => $this->menu->id,
            'route' => 'test',
            'title' => Uuid::uuid(),
        ];
        $this->menu->replaceItems([$menuItem]);

        $controller = $this->getController();
        $result = $controller->postForm('test');

        self::assertEquals(404, $result->getStatusCode());
    }
}
