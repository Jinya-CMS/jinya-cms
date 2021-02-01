<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\GalleryFilePosition;
use App\Database\Menu;
use App\Database\MenuItem;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu/{id}/item', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['position', 'title'])]
#[OpenApiRequest('This action create a new menu item with menu as parent')]
#[OpenApiRequestBody(self::MENU_ITEM_SCHEMA)]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Menu item with required fields', [
    'title' => 0,
    'position' => 0,
])]
#[OpenApiRequestExample('Menu item with multiple fields', self::MENU_ITEM_REQUEST_EXAMPLE)]
#[OpenApiResponse('Successfully created menu item', example: self::MENU_ITEM_EXAMPLE, exampleName: 'Successfully created menu item', statusCode: Action::HTTP_CREATED, ref: MenuItem::class)]
#[OpenApiResponse('Menu not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Menu not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateItemByMenuAction extends MenuItemAction
{
    /**
     * {@inheritDoc}
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $menuId = $this->args['id'];
        $menu = Menu::findById($menuId);
        if (!$menu) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        $menuItem = $this->fillMenuItem();
        $menuItem->menuId = $menuId;
        $menuItem->create();

        return $this->respond($menuItem->format(), Action::HTTP_CREATED);
    }
}
