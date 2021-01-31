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
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user', JinyaAction::POST)]
#[Authenticated(role: Authenticated::ADMIN)]
#[RequiredFields(['artistName', 'email', 'password'])]
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
#[OpenApiRequestExample('Artist with required fields', [
    'email' => OpenApiResponse::FAKER_EMAIL,
    'password' => OpenApiResponse::FAKER_PASSWORD,
    'artistName' => OpenApiResponse::FAKER_USERNAME,
])]
#[OpenApiResponse('Successfully created the artist', statusCode: Action::HTTP_CREATED, ref: Artist::class)]
#[OpenApiResponse('Email exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Email exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Email exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateArtistAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws ConflictException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $artist = new Artist();
        $artist->setPassword($body['password']);
        $artist->enabled = $body['enabled'] ?? false;
        $artist->roles = $body['roles'] ?? [];
        $artist->email = $body['email'];
        $artist->artistName = $body['artistName'];
        try {
            $artist->create();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Email exists');
        }

        return $this->respond($artist->format(), Action::HTTP_CREATED);
    }
}
