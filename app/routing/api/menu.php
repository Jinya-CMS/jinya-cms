<?php

use App\Web\Actions\Menu\CreateMenuAction;
use App\Web\Actions\Menu\DeleteMenuAction;
use App\Web\Actions\Menu\GetMenuByIdAction;
use App\Web\Actions\Menu\Items\CreateItemByMenuAction;
use App\Web\Actions\Menu\Items\CreateItemByMenuItemAction;
use App\Web\Actions\Menu\Items\DeleteItemAction;
use App\Web\Actions\Menu\Items\GetItemsByMenuAction;
use App\Web\Actions\Menu\Items\GetItemsByMenuItemAction;
use App\Web\Actions\Menu\Items\MoveItemParentToItemAction;
use App\Web\Actions\Menu\Items\ResetItemParentAction;
use App\Web\Actions\Menu\Items\UpdateItemAction;
use App\Web\Actions\Menu\Items\UpdateItemByMenuItemAction;
use App\Web\Actions\Menu\ListAllMenusAction;
use App\Web\Actions\Menu\UpdateMenuAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use App\Web\Middleware\CheckRequiredOneOfFieldsMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('menu', function (RouteCollectorProxy $group) {
        $group->get('', ListAllMenusAction::class);
        $group->post('', CreateMenuAction::class)->add(new CheckRequiredFieldsMiddleware(['name']));
        $group->get('/{id}', GetMenuByIdAction::class);
        $group->put('/{id}', UpdateMenuAction::class);
        $group->delete('/{id}', DeleteMenuAction::class);
        $group->group('/{id}/item', function (RouteCollectorProxy $item) {
            $item->get('', GetItemsByMenuAction::class);
            $item
                ->post('', CreateItemByMenuAction::class)
                ->add(new CheckRequiredOneOfFieldsMiddleware(['artist', 'page', 'form', 'gallery', 'segmentPage']))
                ->add(new CheckRequiredFieldsMiddleware(['position', 'title']));
        });
        $group->put('/{id}/item/{menuItemId}/move/parent/one/level/up', MoveItemParentToItemAction::class);
        $group->group('-item', function (RouteCollectorProxy $item) {
            $item->delete('/{menuItemId}', DeleteItemAction::class);
            $item->put('/{menuItemId}', UpdateItemAction::class);
            $item->put('/{menuItemId}/move/parent/to/item/{newParent}', MoveItemParentToItemAction::class);
            $item->put('/{menuItemId}/move/parent/to/menu/{menuId}', ResetItemParentAction::class);
            $item
                ->post('/{menuItemId}/item', CreateItemByMenuItemAction::class)
                ->add(new CheckRequiredOneOfFieldsMiddleware(['artist', 'page', 'form', 'gallery', 'segmentPage']))
                ->add(new CheckRequiredFieldsMiddleware(['position', 'title']));
        });
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};