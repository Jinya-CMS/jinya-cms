<?php

namespace App\Web\Actions\ApiKey;

use App\Database\ApiKey;
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
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/api_key', JinyaAction::GET)]
#[Authenticated]
#[OpenApiRequest('This action lists all api keys')]
#[OpenApiListResponse('Successfully got the api keys', example: [
    'remoteAddress' => OpenApiResponse::FAKER_IPV4,
    'validSince' => OpenApiResponse::FAKER_ISO8601,
    'userAgent' => OpenApiResponse::FAKER_USER_AGENT,
    'key' => OpenApiResponse::FAKER_SHA1,
], exampleName: 'List of API keys', ref: ApiKey::class)]
class ListAllApiKeysAction extends Action
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
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        return $this->respondList($this->formatIterator(ApiKey::findByArtist($currentArtist->id)));
    }
}
