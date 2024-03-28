<?php

namespace Jinya\Tests\Web\Controllers\Menu\Items;

use App\Database\Menu;
use App\Database\MenuItem;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\Menu\Items\ResetMenuItemParentAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class ResetMenuItemParentActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
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
        $action = new ResetMenuItemParentAction();
        $result = $action($request, $response, ['menuId' => $menu->id, 'menuItemId' => $menuItem->id]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeItemNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Menu item not found');
        $menu = new Menu();
        $menu->name = Uuid::uuid();
        $menu->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new ResetMenuItemParentAction();
        $result = $action($request, $response, ['menuId' => $menu->id, 'menuItemId' => -1]);
    }
}
