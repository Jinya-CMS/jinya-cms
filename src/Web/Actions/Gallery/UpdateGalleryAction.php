<?php

namespace App\Web\Actions\Gallery;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{id}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action updates the given gallery')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestBody([
    'name' => ['type' => 'string'],
    'description' => ['type' => 'string'],
    'orientation' => ['type' => 'string', 'enum' => [Gallery::ORIENTATION_VERTICAL, Gallery::ORIENTATION_HORIZONTAL]],
    'type' => ['type' => 'string', 'enum' => [Gallery::TYPE_MASONRY, Gallery::TYPE_SEQUENCE]],
])]
#[OpenApiRequestExample('Gallery with all fields', [
    'name' => OpenApiResponse::FAKER_WORD,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
    'orientation' => Gallery::ORIENTATION_VERTICAL,
    'type' => Gallery::TYPE_MASONRY,
])]
#[OpenApiResponse('Successfully updated the gallery', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Name exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Name exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Name exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class UpdateGalleryAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $gallery = Gallery::findById($id);
        if ($gallery === null) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        if (isset($body['name'])) {
            $gallery->name = $body['name'];
        }

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
            $gallery->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}