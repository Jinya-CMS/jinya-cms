<?php

namespace Jinya\Tests\Database;

use App\Database\File;
use App\Database\Menu;
use App\Database\MenuItem;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\UuidGenerator;

class MenuTest extends DatabaseAwareTestCase
{

    private File $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file = new File();
        $this->file->name = UuidGenerator::generateV4();
        $this->file->path = UuidGenerator::generateV4();
        $this->file->create();
    }

    private function createMenu(string $name = 'Testmenu', bool $execute = true): Menu
    {
        $menu = new Menu();
        $menu->name = $name;
        $menu->logo = $this->file->getIdAsInt();

        if ($execute) {
            $menu->create();
        }

        return $menu;
    }

    public function testUpdate(): void
    {
        $menu = $this->createMenu();
        $menu->name = 'Test';
        $menu->update();

        $found = Menu::findById($menu->getIdAsInt());
        $this->assertEquals($menu, $found);
    }

    public function testCreate(): void
    {
        $menu = $this->createMenu(execute: false);
        $menu->create();

        $found = Menu::findById($menu->getIdAsInt());
        $this->assertEquals($menu, $found);
    }

    public function testFormat(): void
    {
        $menu = $this->createMenu(execute: false);
        $menu->logo = null;
        $menu->create();
        $this->assertEquals(['name' => $menu->name, 'id' => $menu->getIdAsInt()], $menu->format());
    }

    public function testFormatWithLogo(): void
    {
        $menu = $this->createMenu();
        $this->assertEquals(['name' => $menu->name, 'id' => $menu->getIdAsInt(), 'logo' => ['name' => $this->file->name, 'id' => $this->file->id]], $menu->format());
    }

    public function testGetItems(): void
    {
        $menu = $this->createMenu();
        $menuItem = new MenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->position = 1;
        $menuItem->title = 'Testentry';
        $menuItem->route = '#';
        $menuItem->create();

        $parent = new MenuItem();
        $parent->menuId = $menu->getIdAsInt();
        $parent->position = 2;
        $parent->title = 'Testentry2';
        $parent->route = '#';
        $parent->create();

        $menuItem = new MenuItem();
        $menuItem->parentId = $parent->getIdAsInt();
        $menuItem->position = 1;
        $menuItem->title = 'Testentry2';
        $menuItem->route = '#';
        $menuItem->create();

        $this->assertCount(2, iterator_to_array($menu->getItems()));
    }

    public function testGetItemsEmpty(): void
    {
        $menu = $this->createMenu();
        $this->assertCount(0, iterator_to_array($menu->getItems()));
    }

    public function testDelete(): void
    {
        $menu = $this->createMenu();
        $menu->delete();

        $found = Menu::findById($menu->getIdAsInt());
        $this->assertNull($found);
    }

    public function testDeleteNotExisting(): void
    {
        $menu = $this->createMenu();
        $menu->delete();
        $menu->delete();

        $found = Menu::findById($menu->getIdAsInt());
        $this->assertNull($found);
    }

    public function testFindByKeyword(): void
    {
        $this->createMenu();
        $this->createMenu('Testmenu2');
        $this->createMenu('Testmenu3');
        $this->createMenu('Testmenu4');

        $found = Menu::findByKeyword('3');
        $this->assertCount(1, iterator_to_array($found));
    }

    public function testFindById(): void
    {
        $menu = $this->createMenu();
        $found = Menu::findById($menu->getIdAsInt());

        $this->assertEqualsIgnoringCase($menu, $found);
    }

    public function testFindByIdNotExistent(): void
    {
        $found = Menu::findById(-1);

        $this->assertNull($found);
    }

    public function testGetLogo(): void
    {
        $menu = $this->createMenu();
        $this->assertEquals($this->file->id, $menu->getLogo()->id);
    }

    public function testGetLogoNull(): void
    {
        $menu = $this->createMenu(execute: false);
        $menu->logo = null;
        $menu->create();
        $this->assertNull($menu->logo);
    }

    public function testFindAll(): void
    {
        $this->createMenu();
        $this->createMenu('Testmenu2');
        $this->createMenu('Testmenu3');
        $this->createMenu('Testmenu4');

        $all = Menu::findAll();
        $this->assertCount(4, iterator_to_array($all));
    }
}
