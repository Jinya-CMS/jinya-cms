<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get menu items by menu
 */
class GetMenuItemsByMenuAction extends MenuItemAction
{
    /**
     * Gets all menu items in the given menu recursively
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
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
