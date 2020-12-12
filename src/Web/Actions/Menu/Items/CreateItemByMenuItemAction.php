<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateItemByMenuItemAction extends MenuItemAction
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $menuItemId = $this->args['menuItemId'];
        $menuItem = MenuItem::findById($menuItemId);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        $menuItem = $this->fillMenuItem();
        $menuItem->parentId = $menuItemId;
        $menuItem->create();

        return $this->respond($menuItem->format(), Action::HTTP_CREATED);
    }
}