<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\GalleryFilePosition;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{galleryId}/file/{position}', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given gallery file position')]
#[OpenApiParameter('galleryId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('position', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the gallery file position', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Gallery or gallery file position not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Gallery or gallery file position not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeletePositionAction extends Action
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
        $galleryId = $this->args['galleryId'];
        $position = $this->args['position'];
        $galleryFilePosition = GalleryFilePosition::findByPosition($galleryId, $position);
        if (!$galleryFilePosition) {
            throw new NoResultException($this->request, 'Gallery file position not found');
        }
        $galleryFilePosition->delete();

        return $this->noContent();
    }
}
