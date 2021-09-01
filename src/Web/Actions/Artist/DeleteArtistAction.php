<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\DeleteLastAdminException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\CreatedContentException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::ADMIN)]
#[OpenApiRequest('This action deletes the given artist')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the artist', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Artist not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Artist not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteArtistAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws ConflictException
     * @throws CreatedContentException
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $artist = Artist::findById($id);
        if (null === $artist) {
            throw new NoResultException($this->request, 'Artist not found');
        }

        try {
            $artist->delete();
        } catch (InvalidQueryException $exception) {
            throw new CreatedContentException($this->request, 'Cannot delete user, has created content');
        } catch (DeleteLastAdminException $exception) {
            throw new ConflictException($this->request, 'Cannot delete last admin');
        }

        return $this->noContent();
    }
}
