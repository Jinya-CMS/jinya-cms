<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/user/{id}', JinyaAction::GET)]
#[Authenticated(role: Authenticated::ADMIN)]
#[OpenApiRequest('This action gets the given artist')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the artist', example: [
    'artistName' => OpenApiResponse::FAKER_NAME,
    'email' => OpenApiResponse::FAKER_EMAIL,
    'profilePicture' => OpenApiResponse::FAKER_SHA1,
    'roles' => ['ROLE_WRITER'],
    'enabled' => true,
    'id' => 1,
    'aboutMe' => OpenApiResponse::FAKER_PARAGRAPH,
], exampleName: 'Returned artist', ref: Artist::class)]
#[OpenApiResponse('Not authenticated', example: OpenApiResponse::INVALID_API_KEY, exampleName: 'Invalid API key', statusCode: Action::HTTP_FORBIDDEN, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
#[OpenApiResponse('Artist not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Artist not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetArtistByIdAction extends Action
{
    protected function action(): Response
    {
        $artist = Artist::findById((int)$this->args['id']);
        if (null === $artist) {
            throw new HttpNotFoundException($this->request, 'Artist not found');
        }

        return $this->respond($artist->format(true));
    }
}
