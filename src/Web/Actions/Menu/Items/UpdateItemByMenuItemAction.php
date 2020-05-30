<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateItemByMenuItemAction extends MenuItemAction
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $menuItemId = $this->args['id'];
        $position = $this->args['position'];
        $menuItem = MenuItem::findByMenuItemAndPosition($menuItemId, $position);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        $menuItem = $this->fillMenuItem($menuItem);
        $menuItem->update();

        return $this->noContent();
    }
}