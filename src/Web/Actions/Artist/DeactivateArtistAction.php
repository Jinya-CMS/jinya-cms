<?php

namespace App\Web\Actions\Artist;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Middleware\RoleMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}/activation', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::ADMIN)]
class DeactivateArtistAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws ConflictException
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $artist = Artist::findById($this->args['id']);
        if (1 === Artist::countAdmins(CurrentUser::$currentUser->getIdAsInt()) && in_array(RoleMiddleware::ROLE_ADMIN, $artist->roles, true)) {
            throw new ConflictException($this->request, 'Cannot disable last admin');
        }

        $artist->enabled = false;
        $artist->update();

        return $this->noContent();
    }
}
