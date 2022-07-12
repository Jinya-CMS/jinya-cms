<?php

namespace Jinya\Tests\Web\Actions\Menu\Items;

use App\Database\Menu;
use App\Database\MenuItem;
use App\Web\Actions\Menu\Items\UpdateMenuItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class UpdateMenuItemActionTest extends TestCase
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
        $request = $request->withParsedBody(['route' => '/']);
        $response = new Response();
        $action = new UpdateMenuItemAction();
        $result = $action($request, $response, ['menuItemId' => $menuItem->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Menu item not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdateMenuItemAction();
        $action($request, $response, ['menuItemId' => -1]);
    }
}
