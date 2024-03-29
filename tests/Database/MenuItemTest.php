<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\MenuItem;
use App\Database\SegmentPage;
use App\Database\SimplePage;
use App\Tests\DatabaseAwareTestCase;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use LogicException;
use RuntimeException;

class MenuItemTest extends DatabaseAwareTestCase
{

    private function createMenuItem(string $route = '#', string $title = 'Testtitle', int $position = 1): MenuItem
    {
        $menuItem = new MenuItem();
        $menuItem->route = $route;
        $menuItem->title = $title;
        $menuItem->position = $position;

        return $menuItem;
    }

    public function testCreateArtist(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->artistId = CurrentUser::$currentUser->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findById($menuItem->getIdAsInt());
        $this->assertEquals($menuItem->artistId, $found->artistId);
    }

    public function testGetArtist(): void
    {
        $menuItem = $this->createMenuItem();
        $menuItem->artistId = CurrentUser::$currentUser->getIdAsInt();
        $artist = $menuItem->getArtist();
        $this->assertEquals(CurrentUser::$currentUser, $artist);
    }

    public function testFormatArtist(): void
    {
        $menuItem = $this->createMenuItem();
        $menuItem->artistId = CurrentUser::$currentUser->getIdAsInt();
        $format = $menuItem->format();

        $this->assertArrayHasKey('artist', $format);
        /** @var array<string, string> $artist */
        $artist = $format['artist'];
        $this->assertArrayHasKey('id', $artist);
        $this->assertArrayHasKey('artistName', $artist);
        $this->assertArrayHasKey('email', $artist);
        $this->checkFormatFields($menuItem);
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

    public function testCreateForm(): void
    {
        $menu = $this->createMenu();

        $form = $this->createForm();
        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->formId = $form->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findById($menuItem->getIdAsInt());

        $this->assertEquals($form->getIdAsInt(), $found->formId);
    }

    public function testGetForm(): void
    {
        $form = $this->createForm();
        $menuItem = $this->createMenuItem();
        $menuItem->formId = $form->getIdAsInt();

        $this->assertEquals($form->format(), $menuItem->getForm()->format());
    }

    public function testFormatForm(): void
    {
        $menu = $this->createMenu();
        $form = $this->createForm();
        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->formId = $form->getIdAsInt();
        $format = $menuItem->format();

        $this->assertArrayHasKey('form', $format);
        /** @var array<string, string> $formattedForm */
        $formattedForm = $format['form'];
        $this->assertArrayHasKey('id', $formattedForm);
        $this->assertArrayHasKey('title', $formattedForm);
        $this->checkFormatFields($menuItem);
    }

    private function createSimplePage(): SimplePage
    {
        $page = new SimplePage();
        $page->title = 'Test';
        $page->content = 'Test';
        $page->create();

        return $page;
    }

    public function testCreatePage(): void
    {
        $menu = $this->createMenu();

        $page = $this->createSimplePage();
        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->pageId = $page->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findById($menuItem->getIdAsInt());

        $this->assertEquals($page->getIdAsInt(), $found->pageId);
    }

    public function testGetPage(): void
    {
        $page = $this->createSimplePage();
        $menuItem = $this->createMenuItem();
        $menuItem->pageId = $page->getIdAsInt();

        $this->assertEquals($page->format(), $menuItem->getPage()->format());
    }

    public function testFormatPage(): void
    {
        $page = $this->createSimplePage();
        $menuItem = $this->createMenuItem();
        $menuItem->pageId = $page->getIdAsInt();
        $format = $menuItem->format();

        $this->assertArrayHasKey('page', $format);
        /** @var array<string, string> $formattedPage */
        $formattedPage = $format['page'];
        $this->assertArrayHasKey('id', $formattedPage);
        $this->assertArrayHasKey('title', $formattedPage);
        $this->checkFormatFields($menuItem);
    }

    private function createSegmentPage(): SegmentPage
    {
        $page = new SegmentPage();
        $page->name = 'Test';
        $page->create();

        return $page;
    }

    public function testCreateSegmentPage(): void
    {
        $menu = $this->createMenu();

        $page = $this->createSegmentPage();
        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->segmentPageId = $page->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findById($menuItem->getIdAsInt());

        $this->assertEquals($page->getIdAsInt(), $found->segmentPageId);
    }

    public function testGetSegmentPage(): void
    {
        $page = $this->createSegmentPage();
        $menuItem = $this->createMenuItem();
        $menuItem->segmentPageId = $page->getIdAsInt();

        $this->assertEquals($page->format(), $menuItem->getSegmentPage()->format());
    }

    public function testFormatSegmentPage(): void
    {
        $page = $this->createSegmentPage();
        $menuItem = $this->createMenuItem();
        $menuItem->segmentPageId = $page->getIdAsInt();
        $format = $menuItem->format();

        $this->assertArrayHasKey('segmentPage', $format);
        /** @var array<string, string> $formattedSegmentPage */
        $formattedSegmentPage = $format['segmentPage'];
        $this->assertArrayHasKey('id', $formattedSegmentPage);
        $this->assertArrayHasKey('name', $formattedSegmentPage);
        $this->checkFormatFields($menuItem);
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = 'Gallery';
        $gallery->create();

        return $gallery;
    }

    public function testCreateGallery(): void
    {
        $menu = $this->createMenu();

        $gallery = $this->createGallery();
        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->galleryId = $gallery->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findById($menuItem->getIdAsInt());

        $this->assertEquals($gallery->getIdAsInt(), $found->galleryId);
    }

    public function testGetGallery(): void
    {
        $gallery = $this->createGallery();
        $menuItem = $this->createMenuItem();
        $menuItem->galleryId = $gallery->getIdAsInt();

        $this->assertEquals($gallery->format(), $menuItem->getGallery()->format());
    }

    private function createBlogCategory(): BlogCategory
    {
        $blogCategory = new BlogCategory();
        $blogCategory->name = 'BlogCategory';
        $blogCategory->create();

        return $blogCategory;
    }

    public function testCreateBlogCategory(): void
    {
        $menu = $this->createMenu();

        $blogCategory = $this->createBlogCategory();
        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->categoryId = $blogCategory->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findById($menuItem->getIdAsInt());

        $this->assertEquals($blogCategory->getIdAsInt(), $found->categoryId);
    }

    public function testGetBlogCategory(): void
    {
        $blogCategory = $this->createBlogCategory();
        $menuItem = $this->createMenuItem();
        $menuItem->categoryId = $blogCategory->getIdAsInt();

        $this->assertEquals($blogCategory->format(), $menuItem->getBlogCategory()->format());
    }

    public function testFormatGallery(): void
    {
        $gallery = $this->createGallery();
        $menuItem = $this->createMenuItem();
        $menuItem->galleryId = $gallery->getIdAsInt();
        $format = $menuItem->format();

        $this->assertArrayHasKey('gallery', $format);
        /** @var array<string, string> $formattedGallery */
        $formattedGallery = $format['gallery'];
        $this->assertArrayHasKey('id', $formattedGallery);
        $this->assertArrayHasKey('name', $formattedGallery);
        $this->checkFormatFields($menuItem);
    }

    public function testFormatNone(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $this->checkFormatFields($menuItem);
    }


    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        MenuItem::findAll();
    }

    public function testFindByRoute(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $found = MenuItem::findByRoute($parent->route);
        $this->assertEquals($parent, $found);
    }

    public function testFindByRouteNotFound(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $found = MenuItem::findByRoute('notfound');
        $this->assertNull($found);
    }

    public function testMove(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->position = 1;
        $menuItem->create();

        $menuItem2 = $this->createMenuItem();
        $menuItem2->parentId = $menuItem->getIdAsInt();
        $menuItem2->create();

        $menuItem->move(0);

        $foundItems = iterator_to_array(MenuItem::findById($parent->getIdAsInt())->getItems());
        $this->assertCount(2, $foundItems);
        $this->assertCount(1, iterator_to_array($foundItems[0]->getItems()));
    }

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        MenuItem::findByKeyword('Test');
    }

    public function testGetItems(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->create();

        $menuItem2 = $this->createMenuItem();
        $menuItem2->parentId = $menuItem->getIdAsInt();
        $menuItem2->create();

        $this->assertCount(2, iterator_to_array($parent->getItems()));
    }

    public function testUpdate(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $parent->route = 'Test';
        $parent->update();

        $found = MenuItem::findById($parent->getIdAsInt());
        $this->assertEquals($parent, $found);
    }

    public function testFindByMenuItemAndPosition(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->position = 1;
        $menuItem->create();

        $found = MenuItem::findByMenuItemAndPosition($parent->getIdAsInt(), 0);
        $this->assertEquals($menuItem, $found);
    }

    public function testFindByMenuItemAndPositionNotFound(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->position = 1;
        $menuItem->create();

        $found = MenuItem::findByMenuItemAndPosition($parent->getIdAsInt(), -1);
        $this->assertNull($found);
    }

    public function testCreateNoneMenuParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $found = MenuItem::findById($parent->getIdAsInt());
        $this->assertEquals($parent, $found);
    }

    public function testCreateNoneMenuItemParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $item = $this->createMenuItem();
        $item->parentId = $parent->getIdAsInt();
        $item->create();

        $found = MenuItem::findById($item->getIdAsInt());
        $this->assertEquals($item, $found);
    }

    public function testCreateNone(): void
    {
        $this->expectException(LogicException::class);
        $item = $this->createMenuItem();
        $item->create();
    }


    public function testFindById(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findById($menuItem->getIdAsInt());
        $this->assertEquals($menuItem, $found);
    }

    public function testGetMenu(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $this->assertEquals($menu, $menuItem->getMenu());
    }

    public function testGetMenuNoMenu(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->create();

        $this->assertNull($menuItem->getMenu());
    }

    public function testGetParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->create();

        $this->assertEquals($parent, $menuItem->getParent());
    }

    public function testGetParentNoParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $this->assertNull($menuItem->getParent());
    }

    public function testDelete(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $menuItem->delete();
        $found = MenuItem::findById($menuItem->getIdAsInt());

        $this->assertNull($found);
    }

    public function testDeleteNotExistent(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $menuItem->delete();
        $menuItem->delete();
        $found = MenuItem::findById($menuItem->getIdAsInt());

        $this->assertNull($found);
    }

    public function testFormatRecursive(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->position = 2;
        $menuItem->create();

        $menuItem2 = $this->createMenuItem();
        $menuItem2->parentId = $menuItem->getIdAsInt();
        $menuItem2->create();

        $formatted = $parent->formatRecursive();
        $this->assertArrayHasKey('items', $formatted);
        /** @var array<string, string> $items */
        $items = $formatted['items'];
        $this->assertContains($menuItem->formatRecursive(), $items);
    }

    public function testFindByMenuAndPosition(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findByMenuAndPosition($menu->getIdAsInt(), $menuItem->position);
        $this->assertEquals($menuItem, $found);
    }

    public function testFindByMenuAndPositionNotFound(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->create();

        $found = MenuItem::findByMenuAndPosition($menu->getIdAsInt(), -1);
        $this->assertNull($found);
    }

    public function testEmptyChildItems(): void
    {
        $menuItem = $this->createMenuItem();

        $this->assertNull($menuItem->getPage());
        $this->assertNull($menuItem->getSegmentPage());
        $this->assertNull($menuItem->getForm());
        $this->assertNull($menuItem->getGallery());
        $this->assertNull($menuItem->getArtist());
    }

    /**
     * @return Menu
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function checkFormatFields(MenuItem $menuItem): void
    {
        $this->assertArrayHasKey('id', $menuItem->format());
        $this->assertArrayHasKey('position', $menuItem->format());
        $this->assertArrayHasKey('highlighted', $menuItem->format());
        $this->assertArrayHasKey('title', $menuItem->format());
        $this->assertArrayHasKey('route', $menuItem->format());
    }
}
