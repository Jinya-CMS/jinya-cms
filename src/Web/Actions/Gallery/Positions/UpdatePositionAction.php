<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\GalleryFilePosition;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{galleryId}/file/{position}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
#[OpenApiRequest('This action updates a gallery file position')]
#[OpenApiRequestBody([
    'file' => ['type' => 'integer'],
    'newPosition' => ['type' => 'integer'],
])]
#[OpenApiParameter('galleryId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('position', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Gallery file position with required fields', [
    'file' => 0,
    'newPosition' => 0,
])]
#[OpenApiResponse('Successfully updated gallery file position', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Position or file not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Position or file not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class UpdatePositionAction extends Action
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
        $galleryId = $this->args['galleryId'];
        $position = $this->args['position'];
        $galleryFilePosition = GalleryFilePosition::findByPosition($galleryId, $position);

        if (!$galleryFilePosition) {
            throw new NoResultException($this->request, 'Position not found');
        }

        $body = $this->request->getParsedBody();
        $fileId = $body['file'] ?? null;

        if ($fileId) {
            $file = File::findById($fileId);
            if (!$file) {
                throw new NoResultException($this->request, 'File not found');
            }

            $galleryFilePosition->fileId = $fileId;
        }

        if (isset($body['newPosition'])) {
            $galleryFilePosition->move($body['newPosition']);
        }

        return $this->noContent();
    }
}
