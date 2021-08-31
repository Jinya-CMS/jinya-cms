<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/page', JinyaAction::GET)]
#[Authenticated(Authenticated::READER)]
#[OpenApiRequest('This action gets all simple pages')]
#[OpenApiResponse('Successfully got the simple page', example: [
    'id' => 1,
    'content' => OpenApiResponse::FAKER_PARAGRAPH,
    'title' => OpenApiResponse::FAKER_WORD,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Returned simple page', statusCode: Action::HTTP_CREATED, ref: SimplePage::class)]
class ListAllSimplePagesAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(SimplePage::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(SimplePage::findAll()));
    }
}
