<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\MenuItem;
use App\Database\ModernPage;
use App\Database\ClassicPage;
use App\Tests\DatabaseAwareTestCase;
use Jinya\Database\Exception\NotNullViolationException;

class MenuItemTest extends DatabaseAwareTestCase
{
    private function createMenuItem(): array
    {
        return [
            'route' => '#',
            'title' => 'Testtitle',
        ];
    }

    private function createForm(): Form
    {
        $form = new Form();
        $form->description = 'Test description';
        $form->title = 'Testform';
        $form->toAddress = 'noreply@example.com';

        $form->create();

        return $form;
    }

    public function testArtist(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['artistId'] = CurrentUser::$currentUser->id;
        $menu->replaceItems([$menuItem]);

        /** @var MenuItem $found */
        $found = $menu->getItems()->current();
        self::assertEquals(CurrentUser::$currentUser, $found->getArtist());

        $format = $found->format();

        self::assertArrayHasKey('artist', $format);
        /** @var array<string, string> $artist */
        $artist = $format['artist'];
        self::assertArrayHasKey('id', $artist);
        self::assertArrayHasKey('artistName', $artist);
        self::assertArrayHasKey('email', $artist);
        $this->checkFormatFields($found);
    }

    public function testForm(): void
    {
        $menu = $this->createMenu();

        $form = $this->createForm();
        $menuItem = $this->createMenuItem();
        $menuItem['formId'] = $form->id;
        $menu->replaceItems([$menuItem]);

        /** @var MenuItem $found */
        $found = $menu->getItems()->current();
        self::assertEquals($form, $found->getForm());

        $format = $found->format();

        self::assertEquals($form->format(), $found->getForm()->format());

        self::assertArrayHasKey('form', $format);
        /** @var array<string, string> $formattedForm */
        $formattedForm = $format['form'];
        self::assertArrayHasKey('id', $formattedForm);
        self::assertArrayHasKey('title', $formattedForm);
        $this->checkFormatFields($found);
    }

    private function createClassicPage(): ClassicPage
    {
        $page = new ClassicPage();
        $page->title = 'Test';
        $page->content = 'Test';
        $page->create();

        return $page;
    }

    public function testClassicPage(): void
    {
        $menu = $this->createMenu();

        $page = $this->createClassicPage();
        $menuItem = $this->createMenuItem();
        $menuItem['pageId'] = $page->id;
        $menu->replaceItems([$menuItem]);

        /** @var MenuItem $found */
        $found = $menu->getItems()->current();
        self::assertEquals($page, $found->getClassicPage());

        $format = $found->format();

        self::assertEquals($page->format(), $found->getClassicPage()->format());

        self::assertArrayHasKey('page', $format);
        /** @var array<string, string> $formattedPage */
        $formattedPage = $format['page'];
        self::assertArrayHasKey('id', $formattedPage);
        self::assertArrayHasKey('title', $formattedPage);
        $this->checkFormatFields($found);
    }

    private function createModernPage(): ModernPage
    {
        $page = new ModernPage();
        $page->name = 'Test';
        $page->create();

        return $page;
    }

    public function testModernPage(): void
    {
        $menu = $this->createMenu();

        $page = $this->createModernPage();
        $menuItem = $this->createMenuItem();
        $menuItem['modernPageId'] = $page->id;
        $menu->replaceItems([$menuItem]);

        /** @var MenuItem $found */
        $found = $menu->getItems()->current();
        self::assertEquals($page, $found->getModernPage());

        $format = $found->format();

        self::assertEquals($page->format(), $found->getModernPage()->format());

        self::assertArrayHasKey('modernPage', $format);
        /** @var array<string, string> $formattedPage */
        $formattedPage = $format['modernPage'];
        self::assertArrayHasKey('id', $formattedPage);
        self::assertArrayHasKey('name', $formattedPage);
        $this->checkFormatFields($found);
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = 'Gallery';
        $gallery->create();

        return $gallery;
    }

    public function testGallery(): void
    {
        $menu = $this->createMenu();

        $gallery = $this->createGallery();
        $menuItem = $this->createMenuItem();
        $menuItem['galleryId'] = $gallery->id;
        $menu->replaceItems([$menuItem]);

        /** @var MenuItem $found */
        $found = $menu->getItems()->current();
        self::assertEquals($gallery, $found->getGallery());

        $format = $found->format();

        self::assertEquals($gallery->format(), $found->getGallery()->format());

        self::assertArrayHasKey('gallery', $format);
        /** @var array<string, string> $formattedGallery */
        $formattedGallery = $format['gallery'];
        self::assertArrayHasKey('id', $formattedGallery);
        self::assertArrayHasKey('name', $formattedGallery);
        $this->checkFormatFields($found);
    }

    private function createBlogCategory(): BlogCategory
    {
        $blogCategory = new BlogCategory();
        $blogCategory->name = 'BlogCategory';
        $blogCategory->create();

        return $blogCategory;
    }

    public function testBlogCategory(): void
    {
        $menu = $this->createMenu();

        $category = $this->createBlogCategory();
        $menuItem = $this->createMenuItem();
        $menuItem['categoryId'] = $category->id;
        $menu->replaceItems([$menuItem]);

        /** @var MenuItem $found */
        $found = $menu->getItems()->current();
        self::assertEquals($category, $found->getBlogCategory());

        $format = $found->format();

        self::assertEquals($category->format(), $found->getBlogCategory()->format());

        self::assertArrayHasKey('category', $format);
        /** @var array<string, string> $formattedCategory */
        $formattedCategory = $format['category'];
        self::assertArrayHasKey('id', $formattedCategory);
        self::assertArrayHasKey('name', $formattedCategory);
        $this->checkFormatFields($found);
    }

    public function testFormatNone(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menu->replaceItems([$menuItem]);

        /** @var MenuItem $found */
        $found = $menu->getItems()->current();

        $this->checkFormatFields($found);
    }

    public function testFindByRoute(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menu->replaceItems([$menuItem]);

        $found = MenuItem::findByRoute($menuItem['route']);
        $item = $menu->getItems()->current();

        self::assertEquals($item, $found);
    }

    public function testFindByRouteNotFound(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menu->replaceItems([$menuItem]);

        $found = MenuItem::findByRoute('notfound');
        self::assertNull($found);
    }

    public function testGetItems(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $item = $menu->getItems()->current();

        $this->assertCount(3, iterator_to_array($item->getItems()));
    }

    public function testFindByMenuItemAndPosition(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $parent = $menu->getItems()->current();

        $found = MenuItem::findByMenuItemAndPosition($parent->id, 0);
        self::assertEquals($parent->getItems()->current()->format(), $found->format());
    }

    public function testFindByMenuItemAndPositionNotFound(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $item = $menu->getItems()->current();

        $found = MenuItem::findByMenuItemAndPosition($item->id, -1);
        self::assertNull($found);
    }

    public function testGetMenu(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $item = $menu->getItems()->current();

        self::assertEquals($menu, $item->getMenu());
    }

    public function testGetMenuNoMenu(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $item = $menu->getItems()->current()->getItems()->current();

        self::assertNull($item->getMenu());
    }

    public function testGetParent(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $parent = $menu->getItems()->current();
        $item = $parent->getItems()->current();

        self::assertEquals($parent, $item->getParent());
    }

    public function testGetParentNoParent(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $item = $menu->getItems()->current();

        self::assertNull($item->getParent());
    }

    public function testFormatRecursive(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);

        $parent = $menu->getItems()->current();
        $formatted = $parent->formatRecursive();
        self::assertArrayHasKey('items', $formatted);
        /** @var array<string, string> $items */
        $items = $formatted['items'];
        $this->assertNotEmpty($items);
    }

    public function testFindByMenuAndPosition(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);

        $found = MenuItem::findByMenuAndPosition($menu->id, 0);
        self::assertEquals($menuItem['title'], $found->title);
        self::assertEquals($menuItem['route'], $found->route);
    }

    public function testFindByMenuAndPositionNotFound(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem['items'] = [
            $this->createMenuItem(),
            $this->createMenuItem(),
            [
                ...$this->createMenuItem(),
                'items' => [
                    $this->createMenuItem(),
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);

        $found = MenuItem::findByMenuAndPosition($menu->id, -1);
        self::assertNull($found);
    }

    public function testEmptyChildItems(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();

        $menu->replaceItems([$menuItem]);
        $found = MenuItem::findByMenuAndPosition($menu->id, 0);

        self::assertNull($found->getClassicPage());
        self::assertNull($found->getModernPage());
        self::assertNull($found->getForm());
        self::assertNull($found->getGallery());
        self::assertNull($found->getArtist());
    }

    /**
     * @return Menu
     * @throws NotNullViolationException
     */
    public function createMenu(): Menu
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        return $menu;
    }

    /**
     * @param MenuItem $menuItem
     */
    public function checkFormatFields(MenuItem $menuItem): void
    {
        self::assertArrayHasKey('id', $menuItem->format());
        self::assertArrayHasKey('position', $menuItem->format());
        self::assertArrayHasKey('highlighted', $menuItem->format());
        self::assertArrayHasKey('title', $menuItem->format());
        self::assertArrayHasKey('route', $menuItem->format());
    }
}
