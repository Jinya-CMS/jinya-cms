<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to move a menu item to a new parent
 */
class MoveMenuItemParentToItemAction extends Action
{
    /**
     * Moves the given menu item to the new parent set in the body.
     * If the args don't contain the key newParent, the menu item is moved one level up
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
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
        if (array_key_exists('newParent', $this->args)) {
            $newMenuItemParent = $this->args['newParent'];

            $menuItem->menuId = null;
            $menuItem->parentId = $newMenuItemParent;
        } elseif ($menuItem->parentId) {
            $parent = MenuItem::findById($menuItem->parentId);
            if (!$parent) {
                throw new NoResultException($this->request, 'Parent menu item not found');
            }
            if ($parent->parentId !== false && $parent->parentId !== null) {
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
