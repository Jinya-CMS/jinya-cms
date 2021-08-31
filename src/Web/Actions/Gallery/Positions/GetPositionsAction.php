<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\OpenApiGeneration\Attributes\OpenApiArrayResponse;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{galleryId}/file', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action gets all gallery file positions for the given gallery')]
#[OpenApiParameter('galleryId', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiArrayResponse('Successfully got the gallery file positions', example: [
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
], exampleName: 'List of gallery file positions', ref: GalleryFilePosition::class)]
#[OpenApiResponse('Gallery not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Gallery not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetPositionsAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $galleryId = $this->args['galleryId'];
        $gallery = Gallery::findById($galleryId);
        if (!$gallery) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        return $this->respond($this->formatIterator($gallery->getFiles()));
    }
}
