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
        $menu->logo = $this->file->id;

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

        $found = Menu::findById($menu->id);
        $this->assertEquals($menu, $found);
    }

    public function testCreate(): void
    {
        $menu = $this->createMenu(execute: false);
        $menu->create();

        $found = Menu::findById($menu->id);
        $this->assertEquals($menu, $found);
    }

    public function testFormat(): void
    {
        $menu = $this->createMenu(execute: false);
        $menu->logo = null;
        $menu->create();
        $this->assertEquals(['name' => $menu->name, 'id' => $menu->id], $menu->format());
    }

    public function testFormatWithLogo(): void
    {
        $menu = $this->createMenu();
        $this->assertEquals(
            [
                'name' => $menu->name,
                'id' => $menu->id,
                'logo' => ['name' => $this->file->name, 'id' => $this->file->id]
            ],
            $menu->format()
        );
    }

    public function testGetItems(): void
    {
        $menu = $this->createMenu();

        $menuItem = [
            'route' => '#',
            'title' => 'Testtitle',
            'items' => [
                [
                    'route' => '#',
                    'title' => 'Testtitle',
                ],
                [
                    'route' => '#',
                    'title' => 'Testtitle',
                ],
                [
                    'route' => '#',
                    'title' => 'Testtitle',
                    'items' => [
                        [
                            'route' => '#',
                            'title' => 'Testtitle',
                        ]
                    ],
                ],
            ],
        ];

        $menu->replaceItems([$menuItem]);
        $item = $menu->getItems()->current();

        $this->assertCount(3, iterator_to_array($item->getItems()));
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

        $found = Menu::findById($menu->id);
        $this->assertNull($found);
    }

    public function testDeleteNotExisting(): void
    {
        $menu = $this->createMenu();
        $menu->delete();
        $menu->delete();

        $found = Menu::findById($menu->id);
        $this->assertNull($found);
    }

    public function testFindById(): void
    {
        $menu = $this->createMenu();
        $found = Menu::findById($menu->id);

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
