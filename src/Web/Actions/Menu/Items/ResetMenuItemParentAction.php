<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\MenuItem;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu/{menuItemId}/move/parent/to/menu/{menuId}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class ResetMenuItemParentAction extends Action
{
    /**
     * @throws NoResultException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\UniqueFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \JsonException
     */
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
