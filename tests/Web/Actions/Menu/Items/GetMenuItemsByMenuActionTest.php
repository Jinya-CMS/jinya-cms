<?php

namespace Jinya\Tests\Web\Actions\Menu\Items;

use App\Database\Menu;
use App\Database\MenuItem;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Menu\Items\GetMenuItemsByMenuAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetMenuItemsByMenuActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
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
        $action = new GetMenuItemsByMenuAction();
        $result = $action($request, $response, ['id' => $menu->getIdAsInt()]);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeMenuNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Menu not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetMenuItemsByMenuAction();
        $action($request, $response, ['id' => -1]);
    }
}
