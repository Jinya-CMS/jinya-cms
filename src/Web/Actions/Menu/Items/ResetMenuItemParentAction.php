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

#[JinyaAction('/api/menu/{menuItemId}/move/parent/to/menu/{menuId}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action resets the menu item parent to the given menu')]
#[OpenApiParameter('menuItemId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('menuId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully reset the menu items parent', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Menu or menu item not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Menu or menu item not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class ResetMenuItemParentAction extends Action
{
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
