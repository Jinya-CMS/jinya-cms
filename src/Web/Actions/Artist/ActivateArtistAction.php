<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}/activation', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::ADMIN)]
#[OpenApiRequest('This action activates the given artist')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully activated the artist', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Artist not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Artist not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class ActivateArtistAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $artist = Artist::findById($this->args['id']);
        $artist->enabled = true;
        $artist->update();

        return $this->noContent();
    }
}
