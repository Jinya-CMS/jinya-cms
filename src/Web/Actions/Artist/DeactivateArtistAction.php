<?php

namespace App\Web\Actions\Artist;

use App\Authentication\AuthenticationChecker;
use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to deactivate an artist
 */
class DeactivateArtistAction extends Action
{
    /**
     * Dectivates the given artist
     *
     * @return Response
     * @throws ConflictException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $artist = Artist::findById($this->args['id']);
        if ($artist === null) {
            throw new \App\Web\Exceptions\NoResultException($this->request, "Artist doesn't exist");
        }
        if (in_array(AuthenticationChecker::ROLE_ADMIN, $artist->roles, true) && Artist::countAdmins(CurrentUser::$currentUser->getIdAsInt()) === 1) {
            throw new ConflictException($this->request, 'Cannot disable last admin');
        }

        $artist->enabled = false;
        $artist->update();

        return $this->noContent();
    }
}
