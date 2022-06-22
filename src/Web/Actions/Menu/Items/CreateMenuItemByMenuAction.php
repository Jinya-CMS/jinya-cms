<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to create a menu item with menu as parent
 */
class CreateMenuItemByMenuAction extends MenuItemAction
{
    /**
     * Creates the menu item in the body with the given menu as parent
     *
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $menuId = $this->args['id'];
        $menu = Menu::findById($menuId);
        if (!$menu) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        $menuItem = $this->fillMenuItem();
        $menuItem->menuId = $menuId;
        $menuItem->create();

        return $this->respond($menuItem->format(), Action::HTTP_CREATED);
    }
}
