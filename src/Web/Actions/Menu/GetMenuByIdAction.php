<?php

namespace App\Web\Actions\Menu;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu/{id}', JinyaAction::GET)]
#[Authenticated(Authenticated::READER)]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequest('This action gets the given menu')]
#[OpenApiResponse('Successfully got the menu', example: [
    'id' => 1,
    'name' => OpenApiResponse::FAKER_WORD,
    'logo' => [
        'id' => 1,
        'name' => OpenApiResponse::FAKER_WORD,
    ],
], exampleName: 'Returned menu', ref: Menu::class)]
#[OpenApiResponse('Menu not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Menu not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetMenuByIdAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $id = $this->args['id'];
        $menu = Menu::findById($id);
        if (null === $menu) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        return $this->respond($menu->format());
    }
}
