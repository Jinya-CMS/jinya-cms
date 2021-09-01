<?php

namespace App\Web\Actions\Gallery;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['name'])]
#[OpenApiRequest('This action create a new gallery')]
#[OpenApiRequestBody([
    'name' => ['type' => 'string'],
    'description' => ['type' => 'string'],
    'orientation' => ['type' => 'string', 'enum' => [Gallery::ORIENTATION_VERTICAL, Gallery::ORIENTATION_HORIZONTAL]],
    'type' => ['type' => 'string', 'enum' => [Gallery::TYPE_MASONRY, Gallery::TYPE_SEQUENCE]],
])]
#[OpenApiRequestExample('Gallery with required fields', [
    'name' => OpenApiResponse::FAKER_WORD,
])]
#[OpenApiRequestExample('Gallery with all fields', [
    'name' => OpenApiResponse::FAKER_WORD,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
    'orientation' => Gallery::ORIENTATION_VERTICAL,
    'type' => Gallery::TYPE_MASONRY,
])]
#[OpenApiResponse('Successfully created the gallery', example: [
    'id' => 1,
    'name' => OpenApiResponse::FAKER_WORD,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
    'orientation' => Gallery::ORIENTATION_VERTICAL,
    'type' => Gallery::TYPE_MASONRY,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Created gallery', statusCode: Action::HTTP_CREATED, ref: Gallery::class)]
#[OpenApiResponse('Name exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Name exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Name exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateGalleryAction extends Action
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $gallery = new Gallery();
        $gallery->name = $body['name'];

        if (isset($body['description'])) {
            $gallery->description = $body['description'];
        }

        if (isset($body['orientation'])) {
            $gallery->orientation = $body['orientation'];
        }

        if (isset($body['type'])) {
            $gallery->type = $body['type'];
        }

        try {
            $gallery->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->respond($gallery->format(), Action::HTTP_CREATED);
    }
}