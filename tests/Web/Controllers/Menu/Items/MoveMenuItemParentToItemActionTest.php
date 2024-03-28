<?php

namespace Jinya\Tests\Web\Controllers\Menu\Items;

use App\Database\Menu;
use App\Database\MenuItem;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\Menu\Items\MoveMenuItemParentToItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class MoveMenuItemParentToItemActionTest extends DatabaseAwareTestCase
{
    public function test__invokeNewParent(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->id;
        $menuItem->position = 0;
        $menuItem->create();

        $menuItem2 = new MenuItem();
        $menuItem2->title = Uuid::uuid();
        $menuItem2->menuId = $menu->id;
        $menuItem2->position = 0;
        $menuItem2->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem->id, 'newParent' => $menuItem2->id]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNoParent(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->id;
        $menuItem->position = 0;
        $menuItem->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem->id]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeParentParentId(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->id;
        $menuItem->position = 0;
        $menuItem->create();

        $menuItem2 = new MenuItem();
        $menuItem2->title = Uuid::uuid();
        $menuItem2->parentId = $menuItem->id;
        $menuItem2->position = 0;
        $menuItem2->create();

        $menuItem3 = new MenuItem();
        $menuItem3->title = Uuid::uuid();
        $menuItem3->parentId = $menuItem2->id;
        $menuItem3->position = 0;
        $menuItem3->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem3->id]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeParentMenu(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->id;
        $menuItem->position = 0;
        $menuItem->create();

        $menuItem2 = new MenuItem();
        $menuItem2->title = Uuid::uuid();
        $menuItem2->parentId = $menuItem->id;
        $menuItem2->position = 0;
        $menuItem2->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem2->id, 'id' => $menu->id]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Menu item not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $action($request, $response, ['menuItemId' => -1]);
    }
}
