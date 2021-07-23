<?php

namespace Jinya\Tests\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\Database\MenuItem;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MenuItemTest extends TestCase
{

    private function createMenuItem(string $route = '#', string $title = 'Testtitle', int $position = 1): MenuItem
    {
        $menuItem = new MenuItem();
        $menuItem->route = $route;
        $menuItem->title = $title;
        $menuItem->position = $position;

        return $menuItem;
    }

    public function testGetSegmentPage(): void
    {

    }

    public function testGetForm(): void
    {

    }

    public function testGetPage(): void
    {

    }

    public function testGetArtist(): void
    {

    }

    public function testGetGallery(): void
    {

    }

    public function testFormatNone(): void
    {

    }

    public function testFormatForm(): void
    {

    }

    public function testFormatArtist(): void
    {

    }

    public function testFormatGallery(): void
    {

    }

    public function testFormatPage(): void
    {

    }

    public function testFormatSegmentPage(): void
    {

    }

    public function testCreateSegmentPage(): void
    {

    }

    public function testCreatePage(): void
    {

    }

    public function testCreateForm(): void
    {

    }

    public function testCreateGallery(): void
    {

    }

    public function testCreateArtist(): void
    {

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
        $parent->menuId = $menu->id;
        $parent->create();

        $found = MenuItem::findByRoute($parent->route);
        $this->assertEquals($parent, $found);
    }

    public function testFindByRouteNotFound(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $found = MenuItem::findByRoute('notfound');
        $this->assertNull($found);
    }

    public function testMove(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->position = 2;
        $menuItem->create();

        $menuItem2 = $this->createMenuItem();
        $menuItem2->parentId = $menuItem->id;
        $menuItem2->create();

        $menuItem->move(1);

        $foundItems = iterator_to_array(MenuItem::findById($parent->id)->getItems());
        $this->assertCount(2, $foundItems);
        $this->assertCount(1, $foundItems[0]->getItems());
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
        $parent->menuId = $menu->id;
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->create();

        $menuItem2 = $this->createMenuItem();
        $menuItem2->parentId = $menuItem->id;
        $menuItem2->create();

        $this->assertCount(2, $parent->getItems());
    }

    public function testUpdate(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $parent->route = 'Test';
        $parent->update();

        $found = MenuItem::findById($parent->id);
        $this->assertEquals($parent, $found);
    }

    public function testFindByMenuItemAndPosition(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->position = 1;
        $menuItem->create();

        $found = MenuItem::findByMenuItemAndPosition($parent->id, 1);
        $this->assertEquals($menuItem, $found);
    }

    public function testFindByMenuItemAndPositionNotFound(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->position = 1;
        $menuItem->create();

        $found = MenuItem::findByMenuItemAndPosition($parent->id, -1);
        $this->assertNull($found);
    }

    public function testCreateNoneMenuParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $found = MenuItem::findById($parent->id);
        $this->assertEquals($parent, $found);
    }

    public function testCreateNoneMenuItemParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $item = $this->createMenuItem();
        $item->parentId = $parent->id;
        $item->create();

        $found = MenuItem::findById($item->id);
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
        $menuItem->menuId = $menu->id;
        $menuItem->create();

        $found = MenuItem::findById($menuItem->id);
        $this->assertEquals($menuItem, $found);
    }

    public function testGetMenu(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->id;
        $menuItem->create();

        $this->assertEquals($menu, $menuItem->getMenu());
    }

    public function testGetMenuNoMenu(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->create();

        $this->assertNull($menuItem->getMenu());
    }

    public function testGetParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->create();

        $this->assertEquals($parent, $menuItem->getParent());
    }

    public function testGetParentNoParent(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->id;
        $menuItem->create();

        $this->assertNull($menuItem->getParent());
    }

    public function testDelete(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->id;
        $menuItem->create();

        $menuItem->delete();
        $found = MenuItem::findById($menuItem->id);

        $this->assertNull($found);
    }

    public function testDeleteNotExistent(): void
    {
        $menu = $this->createMenu();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->id;
        $menuItem->create();

        $menuItem->delete();
        $menuItem->delete();
        $found = MenuItem::findById($menuItem->id);

        $this->assertNull($found);
    }

    public function testFormatRecursive(): void
    {
        $menu = $this->createMenu();

        $parent = $this->createMenuItem();
        $parent->menuId = $menu->id;
        $parent->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->create();

        $menuItem = $this->createMenuItem();
        $menuItem->parentId = $parent->id;
        $menuItem->position = 2;
        $menuItem->create();

        $menuItem2 = $this->createMenuItem();
        $menuItem2->parentId = $menuItem->id;
        $menuItem2->create();

        $formatted = $parent->formatRecursive();
        $this->assertArrayHasKey('items', $formatted);
        $this->assertContains($menuItem->formatRecursive(), $formatted['items']);
    }

    public function testFindByMenuAndPosition(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->id;
        $menuItem->create();

        $found = MenuItem::findByMenuAndPosition($menu->id, $menuItem->position);
        $this->assertEquals($menuItem, $found);
    }

    public function testFindByMenuAndPositionNotFound(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = $this->createMenuItem();
        $menuItem->menuId = $menu->id;
        $menuItem->create();

        $found = MenuItem::findByMenuAndPosition($menu->id, -1);
        $this->assertNull($found);
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
}
