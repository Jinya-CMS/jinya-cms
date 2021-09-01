<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiListResponse;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action lists all api keys')]
#[OpenApiListResponse('Successfully got the users', example: [
    'artistName' => OpenApiResponse::FAKER_NAME,
    'email' => OpenApiResponse::FAKER_EMAIL,
    'profilePicture' => OpenApiResponse::FAKER_SHA1,
    'roles' => ['ROLE_WRITER'],
    'enabled' => true,
    'id' => 1,
    'aboutMe' => OpenApiResponse::FAKER_PARAGRAPH,
], exampleName: 'List of artists', ref: Artist::class)]
class ListAllArtistsAction extends Action
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
            return $this->respondList($this->formatIterator(Artist::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Artist::findAll()));
    }
}
