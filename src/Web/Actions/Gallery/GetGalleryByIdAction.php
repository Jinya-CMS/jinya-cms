<?php

namespace App\Web\Actions\Gallery;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{id}', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action gets the given gallery')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the gallery', example: [
    'id' => 1,
    'name' => OpenApiResponse::FAKER_WORD,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
    'orientation' => Gallery::ORIENTATION_VERTICAL,
    'type' => Gallery::TYPE_MASONRY,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Some gallery', statusCode: Action::HTTP_CREATED, ref: Gallery::class)]
#[OpenApiResponse('Gallery not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Gallery not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetGalleryByIdAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $gallery = Gallery::findById($id);
        if (null === $gallery) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        return $this->respond($gallery->format());
    }
}
