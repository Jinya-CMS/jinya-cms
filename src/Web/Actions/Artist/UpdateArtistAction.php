<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::ADMIN)]
#[OpenApiRequest('This action create a new artist')]
#[OpenApiRequestBody([
    'email' => ['type' => 'string', 'format' => 'email'],
    'password' => ['type' => 'string', 'format' => 'password'],
    'roles' => [
        'type' => 'array',
        'items' => ['type' => 'string'],
        'enum' => ['ROLE_READER', 'ROLE_WRITER', 'ROLE_ADMIN']
    ],
    'artistName' => ['type' => 'string'],
    'enabled' => ['type' => 'boolean'],
])]
#[OpenApiRequestExample('Artist with all fields', [
    'email' => OpenApiResponse::FAKER_EMAIL,
    'password' => OpenApiResponse::FAKER_PASSWORD,
    'roles' => ['ROLE_READER', 'ROLE_WRITER', 'ROLE_ADMIN'],
    'artistName' => OpenApiResponse::FAKER_USERNAME,
    'enabled' => true,
])]
#[OpenApiResponse('Successfully updated the artist', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Not authenticated', example: OpenApiResponse::INVALID_API_KEY, exampleName: 'Invalid API key', statusCode: Action::HTTP_FORBIDDEN, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
#[OpenApiResponse('Email exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Email exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Email exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class UpdateArtistAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $id = (int) $this->args['id'];
        $artist = Artist::findById($id);
        if (null === $artist) {
            throw new NoResultException($this->request, 'Artist not found');
        }
        $body = $this->request->getParsedBody();
        if (isset($body['email'])) {
            $artist->email = $body['email'];
        }
        if (isset($body['roles'])) {
            $artist->roles = $body['roles'];
        }
        if (isset($body['enabled'])) {
            $artist->enabled = $body['enabled'];
        }
        if (isset($body['artistName'])) {
            $artist->artistName = $body['artistName'];
        }
        if (isset($body['password'])) {
            $artist->setPassword($body['password']);
        }
        try {
            $artist->update();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Email exists');
        }

        return $this->noContent();
    }
}
