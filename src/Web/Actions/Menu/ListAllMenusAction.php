<?php

namespace App\Web\Actions\Menu;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\OpenApiGeneration\Attributes\OpenApiListResponse;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu', JinyaAction::GET)]
#[Authenticated(Authenticated::READER)]
#[OpenApiRequest('This action gets the given menu')]
#[OpenApiListResponse('Successfully got the menus', example: [
    'id' => 1,
    'name' => OpenApiResponse::FAKER_WORD,
    'logo' => [
        'id' => 1,
        'name' => OpenApiResponse::FAKER_WORD,
    ],
], exampleName: 'Returned menus', ref: Menu::class)]
class ListAllMenusAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(Menu::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Menu::findAll()));
    }
}
