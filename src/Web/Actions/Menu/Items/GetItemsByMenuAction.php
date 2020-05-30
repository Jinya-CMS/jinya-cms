<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Menu;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetItemsByMenuAction extends MenuItemAction
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $menuId = $this->args['id'];
        $menu = Menu::findById($menuId);
        if (!$menu) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        return $this->respond($this->formatIteratorRecursive($menu->getItems()));
    }
}