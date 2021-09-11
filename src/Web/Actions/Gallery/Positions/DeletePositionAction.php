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
    protected function action(): Response
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
