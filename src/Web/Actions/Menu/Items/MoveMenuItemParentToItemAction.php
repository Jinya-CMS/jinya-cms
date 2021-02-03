<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\MenuItem;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu-item/{menuItemId}/move/parent/to/item/{newParent}', JinyaAction::PUT)]
#[JinyaAction('/api/menu/{id}/item/{menuItemId}/move/parent/one/level/up', JinyaAction::PUT, name: 'menu_item_move_one_level_up')]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given menu')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('menuItemId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('newParent', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the menu', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Menu not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Menu not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class MoveMenuItemParentToItemAction extends Action
{
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
                $newPosition = $parent->position + 1;
                $menuItem->move($newPosition);
            } else {
                $menuId = $this->args['id'];
                $menuItem->menuId = $menuId;
                $menuItem->parentId = null;
                $newPosition = $parent->position + 1;
                $menuItem->move($newPosition);
            }
        }

        $menuItem->update(false);

        return $this->noContent();
    }
}
