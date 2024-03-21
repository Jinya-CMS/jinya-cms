<?php

namespace Jinya\Tests\Web\Actions\Menu\Items;

use App\Database\Menu;
use App\Database\MenuItem;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Menu\Items\DeleteMenuItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class DeleteMenuItemActionTest extends DatabaseAwareTestCase
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
        $action = new DeleteMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Menu item not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteMenuItemAction();
        $action($request, $response, ['menuItemId' => -1]);
    }
}
