<?php

namespace Jinya\Tests\Theming\Extensions;

use App\Database\Menu;
use App\Database\MenuItem;
use App\Tests\DatabaseAwareTestCase;
use App\Theming\Engine;
use App\Theming\Extensions\MenuExtension;
use Faker\Provider\Uuid;

class MenuExtensionTest extends DatabaseAwareTestCase
{

    private MenuExtension $extension;
    private Menu $menu;

    public function testIsChildActiveMenuItem(): void
    {
        $_SERVER['REQUEST_URI'] = '/active/sub';
        $parent = $this->createMenuItem('active');
        $child = $this->createMenuItem('active/sub');
        $child->parentId = $parent->getIdAsInt();
        $child->menuId = null;
        $child->update();

        $result = $this->extension->isChildActiveMenuItem($parent);
        self::assertTrue($result);
    }

    private function createMenuItem(string $route): MenuItem
    {
        $menuItem = new MenuItem();
        $menuItem->route = $route;
        $menuItem->position = 0;
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->create();

        return $menuItem;
    }

    public function testIsChildActiveMenuItemParentIsActive(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('active');
        $child = $this->createMenuItem('not-active/sub');
        $child->parentId = $parent->getIdAsInt();
        $child->menuId = null;
        $child->update();

        $result = $this->extension->isChildActiveMenuItem($parent);
        self::assertFalse($result);
    }

    public function testIsChildActiveMenuItemNotActive(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('not-active');
        $child = $this->createMenuItem('not-active/sub');
        $child->parentId = $parent->getIdAsInt();
        $child->menuId = null;
        $child->update();

        self::assertFalse($this->extension->isChildActiveMenuItem($parent));
    }

    public function testIsActiveMenuItem(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('active');

        $result = $this->extension->isActiveMenuItem($parent);
        self::assertTrue($result);
    }

    public function testIsActiveMenuItemNotActive(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('not-active');

        self::assertFalse($this->extension->isActiveMenuItem($parent));
    }

    public function testGetActiveMenuItem(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('active');
        $active = $this->extension->getActiveMenuItem();
        self::assertEquals($parent->title, $active->title);
        self::assertEquals($parent->route, $active->route);
    }

    public function testRegister(): void
    {
        $engine = Engine::getPlatesEngine();
        $this->extension->register($engine);

        self::assertTrue($engine->doesFunctionExist('getActiveMenuItem'));
        self::assertTrue($engine->doesFunctionExist('isActiveMenuItem'));
        self::assertTrue($engine->doesFunctionExist('isChildActiveMenuItem'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new MenuExtension();
        $this->menu = new Menu();
        $this->menu->name = 'Test';
        $this->menu->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->menu->delete();
    }
}
