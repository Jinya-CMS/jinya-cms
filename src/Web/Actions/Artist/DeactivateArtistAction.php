<?php

namespace App\Web\Actions\Artist;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Middleware\RoleMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

class DeactivateArtistAction extends Action
{
    /**
     * {@inheritDoc}
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
        if (in_array(RoleMiddleware::ROLE_ADMIN, $artist->roles, true) && 1 === Artist::countAdmins(CurrentUser::$currentUser->getIdAsInt())) {
            throw new ConflictException($this->request, 'Cannot disable last admin');
        }

        $artist->enabled = false;
        $artist->update();

        return $this->noContent();
    }
}
