<?php

namespace Jinya\Cms\Theming\Extensions;

use Jinya\Cms\Database\Menu;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Engine;
use Jinya\Cms\Theming\Extensions\MenuExtension;
use Faker\Provider\Uuid;

class MenuExtensionTest extends DatabaseAwareTestCase
{
    private MenuExtension $extension;
    private Menu $menu;

    private function createMenuItem(string $route): array
    {
        return [
            'route' => $route,
            'title' => Uuid::uuid(),
        ];
    }

    public function testIsChildActiveMenuItem(): void
    {
        $_SERVER['REQUEST_URI'] = '/active/sub';
        $parent = $this->createMenuItem('active');
        $child = $this->createMenuItem('active/sub');

        $parent['items'] = [$child];
        $this->menu->replaceItems([$parent]);

        $items = $this->menu->getItems();

        $result = $this->extension->isChildActiveMenuItem($items->current());
        self::assertTrue($result);
    }

    public function testIsChildActiveMenuItemParentIsActive(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('active');
        $child = $this->createMenuItem('not-active/sub');

        $parent['items'] = [$child];
        $this->menu->replaceItems([$parent]);

        $items = $this->menu->getItems();

        $result = $this->extension->isChildActiveMenuItem($items->current());
        self::assertFalse($result);
    }

    public function testIsChildActiveMenuItemNotActive(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('not-active');
        $child = $this->createMenuItem('not-active/sub');

        $parent['items'] = [$child];
        $this->menu->replaceItems([$parent]);

        $items = $this->menu->getItems();

        self::assertFalse($this->extension->isChildActiveMenuItem($items->current()));
    }

    public function testIsActiveMenuItem(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('active');

        $this->menu->replaceItems([$parent]);

        $items = $this->menu->getItems();

        $result = $this->extension->isActiveMenuItem($items->current());
        self::assertTrue($result);
    }

    public function testIsActiveMenuItemNotActive(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('not-active');

        $this->menu->replaceItems([$parent]);

        $items = $this->menu->getItems();

        self::assertFalse($this->extension->isActiveMenuItem($items->current()));
    }

    public function testGetActiveMenuItem(): void
    {
        $_SERVER['REQUEST_URI'] = '/active';
        $parent = $this->createMenuItem('active');

        $this->menu->replaceItems([$parent]);

        $active = $this->extension->getActiveMenuItem();

        self::assertEquals($parent['title'], $active->title);
        self::assertEquals($parent['route'], $active->route);
    }

    public function testRegister(): void
    {
        $engine = Engine::getPlatesEngine();
        $this->extension->register($engine);

        self::assertTrue($engine->functions->exists('getActiveMenuItem'));
        self::assertTrue($engine->functions->exists('isActiveMenuItem'));
        self::assertTrue($engine->functions->exists('isChildActiveMenuItem'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new MenuExtension();
        $this->menu = new Menu();
        $this->menu->name = 'Test';
        $this->menu->create();
    }
}
