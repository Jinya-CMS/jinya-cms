<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\MenuItem;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu-item/{menuItemId}/move/parent/to/item/{newParent}', JinyaAction::PUT)]
#[JinyaAction('/api/menu/{id}/item/{menuItemId}/move/parent/one/level/up', JinyaAction::PUT, name: 'menu_item_move_one_level_up')]
#[Authenticated(Authenticated::WRITER)]
class MoveMenuItemParentToItemAction extends Action
{
    /**
     * @throws \App\Database\Exceptions\UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \JsonException
     * @throws NoResultException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $menuItemId = $this->args['menuItemId'];
        $menuItem = MenuItem::findById($menuItemId);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }
        if (array_key_exists('newParent', $this->args)) {
            $newMenuItemParent = $this->args['newParent'];

            $menuItem->menuId = null;
            $menuItem->parentId = $newMenuItemParent;
        } elseif ($menuItem->parentId) {
            $parent = MenuItem::findById($menuItem->parentId);
            if ($parent->parentId) {
                $menuItem->parentId = $parent->parentId;
                $menuItem->menuId = null;
            } else {
                $menuId = $this->args['id'];
                $menuItem->menuId = $menuId;
                $menuItem->parentId = null;
            }
            $newPosition = $parent->position + 1;
            $menuItem->move($newPosition);
        }

        $menuItem->update(false);

        return $this->noContent();
    }
}
