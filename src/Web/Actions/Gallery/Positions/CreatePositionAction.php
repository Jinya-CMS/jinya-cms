<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\FormItem;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
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
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{galleryId}/file', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['fileId', 'position'])]
#[OpenApiRequest('This action create a new gallery file position')]
#[OpenApiRequestBody([
    'fileId' => ['type' => 'integer'],
    'position' => ['type' => 'integer'],
])]
#[OpenApiParameter('galleryId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Gallery file position with required fields', [
    'fileId' => 0,
    'position' => 0,
])]
#[OpenApiResponse('Successfully created gallery file position', example: [
    'gallery' => [
        'id' => 0,
        'name' => OpenApiResponse::FAKER_WORD,
        'description' => OpenApiResponse::FAKER_PARAGRAPH,
    ],
    'file' => [
        'path' => OpenApiResponse::FAKER_SHA1,
        'id' => 0,
        'name' => OpenApiResponse::FAKER_WORD,
        'type' => OpenApiResponse::FAKER_MIMETYPE,
    ],
    'id' => 0,
    'position' => 0,
], exampleName: 'Successfully created gallery file position', statusCode: Action::HTTP_CREATED, ref: GalleryFilePosition::class)]
#[OpenApiResponse('Gallery or file not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Gallery or file not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreatePositionAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $galleryId = $this->args['galleryId'];
        $position = $body['position'];
        $file = $body['file'];

        if (!File::findById($file)) {
            throw new NoResultException($this->request, 'File not found');
        }

        if (!Gallery::findById($galleryId)) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $galleryFilePosition = new GalleryFilePosition();
        $galleryFilePosition->fileId = $file;
        $galleryFilePosition->galleryId = $galleryId;
        $galleryFilePosition->position = $position;

        $galleryFilePosition->create();

        return $this->respond($galleryFilePosition->format(), Action::HTTP_CREATED);
    }
}
