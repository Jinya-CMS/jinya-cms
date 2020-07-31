<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\MenuItem;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetItemsByMenuItemAction extends MenuItemAction
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $parentId = $this->args['id'];
        $menuItem = MenuItem::findById($parentId);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        return $this->respond($this->formatIteratorRecursive($menuItem->getItems()));
    }
}