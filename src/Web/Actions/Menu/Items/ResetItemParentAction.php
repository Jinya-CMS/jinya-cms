<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\MenuItem;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

class ResetItemParentAction extends Action
{

    protected function action(): Response
    {
        $menuId = $this->args['menuId'];
        $menuItemId = $this->args['menuItemId'];
        $menuItem = MenuItem::findById($menuItemId);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        $menuItem->menuId = $menuId;
        $menuItem->parentId = null;
        $menuItem->update(false);

        return $this->noContent();
    }
}