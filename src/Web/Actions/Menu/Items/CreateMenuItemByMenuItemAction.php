<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu-item/{menuItemId}/item', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['position', 'title'])]
class CreateMenuItemByMenuItemAction extends MenuItemAction
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
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
