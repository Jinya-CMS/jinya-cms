<?php

namespace App\Web\Actions\Gallery;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\OpenApiGeneration\Attributes\OpenApiListResponse;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action gets all galleries')]
#[OpenApiListResponse('Successfully got the galleries', example: [
    'id' => 1,
    'name' => OpenApiResponse::FAKER_WORD,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
    'orientation' => Gallery::ORIENTATION_VERTICAL,
    'type' => Gallery::TYPE_MASONRY,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Some gallery', statusCode: Action::HTTP_CREATED, ref: Gallery::class)]
class ListAllGalleriesAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(Gallery::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Gallery::findAll()));
    }
}
