<?php

use App\Web\Actions\Menu\CreateMenuAction;
use App\Web\Actions\Menu\DeleteMenuAction;
use App\Web\Actions\Menu\GetMenuByIdAction;
use App\Web\Actions\Menu\Items\CreateItemByMenuAction;
use App\Web\Actions\Menu\Items\CreateItemByMenuItemAction;
use App\Web\Actions\Menu\Items\DeleteItemByMenuAction;
use App\Web\Actions\Menu\Items\DeleteItemByMenuItemAction;
use App\Web\Actions\Menu\Items\GetItemsByMenuAction;
use App\Web\Actions\Menu\Items\GetItemsByMenuItemAction;
use App\Web\Actions\Menu\Items\UpdateItemByMenuAction;
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
            $item->put('/{position}', UpdateItemByMenuAction::class);
            $item->delete('/{position}', DeleteItemByMenuAction::class);
        });
        $group->group('-item/{id}/item', function (RouteCollectorProxy $item) {
            $item->get('', GetItemsByMenuItemAction::class);
            $item
                ->post('', CreateItemByMenuItemAction::class)
                ->add(new CheckRequiredOneOfFieldsMiddleware(['artist', 'page', 'form', 'gallery', 'segmentPage']))
                ->add(new CheckRequiredFieldsMiddleware(['position', 'title']));
            $item->put('/{position}', UpdateItemByMenuItemAction::class);
            $item->delete('/{position}', DeleteItemByMenuItemAction::class);
        });
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};