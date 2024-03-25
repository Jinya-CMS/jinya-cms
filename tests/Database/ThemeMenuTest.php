<?php

namespace Jinya\Tests\Database;

use App\Database\Menu;
use App\Database\ThemeMenu;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeMenuTest extends ThemeTestCase
{
    private Menu $menu;

    public function testFindByThemeNoMenu(): void
    {
        $themeMenus = ThemeMenu::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($themeMenus));
    }

    public function testFindByTheme(): void
    {
        $themeMenu = new ThemeMenu();
        $themeMenu->menuId = $this->menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->name = 'Test';
        $themeMenu->create();

        $themeMenus = ThemeMenu::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($themeMenus));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeMenu::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeMenu = new ThemeMenu();
        $themeMenu->menuId = $this->menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->name = 'Test';
        $themeMenu->create();

        $found = ThemeMenu::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($themeMenu->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeMenu = new ThemeMenu();
        $themeMenu->menuId = $this->menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->name = 'Test';
        $themeMenu->create();

        self::assertNotNull(ThemeMenu::findByThemeAndName($this->theme->id, 'Test'));

        $themeMenu->delete();
        self::assertNull(ThemeMenu::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $themeMenu = new ThemeMenu();
        $themeMenu->menuId = $this->menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->name = 'Test';
        $themeMenu->create();

        $menu = new Menu();
        $menu->name = 'Tempmenu';
        $menu->create();

        $themeMenu->menuId = $menu->id;
        $themeMenu->update();
        $found = ThemeMenu::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($menu->id, $found->menuId);
    }

    public function testCreate(): void
    {
        $themeMenu = new ThemeMenu();
        $themeMenu->menuId = $this->menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->name = 'Test';
        $themeMenu->create();

        self::assertNotNull(ThemeMenu::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testFormat(): void
    {
        $themeMenu = new ThemeMenu();
        $themeMenu->menuId = $this->menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->name = 'Test';
        $themeMenu->create();

        self::assertEquals([
            'name' => 'Test',
            'menu' => $this->menu->format(),
        ], $themeMenu->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $this->menu = $menu;
    }
}
