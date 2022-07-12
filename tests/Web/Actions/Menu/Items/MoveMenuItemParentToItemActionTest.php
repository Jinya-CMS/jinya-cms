<?php

namespace Jinya\Tests\Web\Actions\Menu\Items;

use App\Database\Menu;
use App\Database\MenuItem;
use App\Web\Actions\Menu\Items\MoveMenuItemParentToItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class MoveMenuItemParentToItemActionTest extends TestCase
{

    public function test__invokeNewParent(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->position = 0;
        $menuItem->create();

        $menuItem2 = new MenuItem();
        $menuItem2->title = Uuid::uuid();
        $menuItem2->menuId = $menu->getIdAsInt();
        $menuItem2->position = 0;
        $menuItem2->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem->getIdAsInt(), 'newParent' => $menuItem2->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNoParent(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->position = 0;
        $menuItem->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeParentParentId(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->position = 0;
        $menuItem->create();

        $menuItem2 = new MenuItem();
        $menuItem2->title = Uuid::uuid();
        $menuItem2->parentId = $menuItem->getIdAsInt();
        $menuItem2->position = 0;
        $menuItem2->create();

        $menuItem3 = new MenuItem();
        $menuItem3->title = Uuid::uuid();
        $menuItem3->parentId = $menuItem2->getIdAsInt();
        $menuItem3->position = 0;
        $menuItem3->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem3->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeParentMenu(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->title = Uuid::uuid();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->position = 0;
        $menuItem->create();

        $menuItem2 = new MenuItem();
        $menuItem2->title = Uuid::uuid();
        $menuItem2->parentId = $menuItem->getIdAsInt();
        $menuItem2->position = 0;
        $menuItem2->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new MoveMenuItemParentToItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem2->getIdAsInt(), 'id' => $menu->getIdAsInt()]);
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
