<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Middleware\RoleMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeactivateArtistAction extends Action
{
    /**
     * @inheritDoc
     * @throws UniqueFailedException
     * @throws JsonException
     * @throws ConflictException
     */
    protected function action(): Response
    {
        $artist = Artist::findById($this->args['id']);
        if (Artist::countAdmins() === 1 && in_array(RoleMiddleware::ROLE_ADMIN, $artist->roles, true)) {
            throw new ConflictException($this->request, 'Cannot disable last admin');
        }

        $artist->enabled = false;
        $artist->update();

        return $this->noContent();
    }
}