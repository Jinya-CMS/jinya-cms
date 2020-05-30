<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\MenuItem;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteItemByMenuItemAction extends MenuItemAction
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $menuItemId = $this->args['id'];
        $position = $this->args['position'];
        $menuItem = MenuItem::findByMenuItemAndPosition($menuItemId, $position);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        $menuItem->delete();

        return $this->noContent();
    }
}