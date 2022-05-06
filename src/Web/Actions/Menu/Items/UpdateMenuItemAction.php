<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateMenuItemAction extends MenuItemAction
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $menuItemId = $this->args['menuItemId'];
        $menuItem = MenuItem::findById($menuItemId);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        /** @noinspection PhpParamsInspection */
        $menuItem = $this->fillMenuItem($menuItem);
        $menuItem->update();

        return $this->noContent();
    }
}
