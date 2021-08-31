<?php

namespace App\Web\Actions\Menu;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Menu;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['name'])]
#[OpenApiRequest('This action create a new menu')]
#[OpenApiRequestBody([
    'name' => ['type' => 'string'],
    'logo' => ['type' => 'integer'],
])]
#[OpenApiRequestExample('Menu with required fields', [
    'name' => OpenApiResponse::FAKER_WORD,
])]
#[OpenApiRequestExample('Menu with all fields', [
    'name' => OpenApiResponse::FAKER_WORD,
    'logo' => 1,
])]
#[OpenApiResponse('Successfully created the menu', example: [
    'id' => 1,
    'name' => OpenApiResponse::FAKER_WORD,
    'logo' => [
        'id' => 1,
        'name' => OpenApiResponse::FAKER_WORD,
    ],
], exampleName: 'Returned menu', statusCode: Action::HTTP_CREATED, ref: Menu::class)]
#[OpenApiResponse('Name exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Name exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Name exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
#[OpenApiResponse('File for logo not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'File for logo not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateMenuAction extends Action
{
    /**
     * @inheritDoc
     * @throws ConflictException
     * @throws Exception
     */
    public function action(): Response
    {
        $body = $this->request->getParsedBody();
        $menu = new Menu();
        $menu->name = $body['name'];
        if (isset($body['logo'])) {
            if (File::findById($body['logo'])) {
                $menu->logo = $body['logo'];
            } else {
                throw new NoResultException($this->request, 'File for logo not found');
            }
        } else {
            $menu->logo = null;
        }

        try {
            $menu->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->respond($menu->format(), Action::HTTP_CREATED);
    }
}