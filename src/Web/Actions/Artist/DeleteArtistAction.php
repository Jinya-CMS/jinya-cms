<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\DeleteLastAdminException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\CreatedContentException;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::ADMIN)]
class DeleteArtistAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ConflictException
     * @throws CreatedContentException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
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
