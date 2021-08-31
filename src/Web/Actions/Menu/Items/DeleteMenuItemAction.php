<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu-item/{menuItemId}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given menu item')]
#[OpenApiParameter('menuItemId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the menu item', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Menu item not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Menu item not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteMenuItemAction extends MenuItemAction
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $menuItemId = $this->args['menuItemId'];
        $menuItem = MenuItem::findById($menuItemId);
        if (!$menuItem) {
            throw new NoResultException($this->request, 'Menu item not found');
        }

        $menuItem->delete();

        return $this->noContent();
    }
}
