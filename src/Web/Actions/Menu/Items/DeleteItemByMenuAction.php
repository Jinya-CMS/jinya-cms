<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\MenuItem;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteItemByMenuAction extends MenuItemAction
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $menuId = $this->args['id'];
        $position = $this->args['position'];
        $menuItem = MenuItem::findByMenuAndPosition($menuId, $position);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        $menuItem->delete();

        return $this->noContent();
    }
}