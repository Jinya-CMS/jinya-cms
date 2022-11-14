<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to activate an artist
 */
class UpdateArtistAction extends Action
{
    /**
     * Activates the given artist
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $artist = Artist::findById($this->args['id']);
        if ($artist === null) {
            throw new NoResultException($this->request, 'Artist not found');
        }
        if ($this->body['password']) {
            $artist->setPassword($this->body['password']);
        }
        if ($this->body['artistName']) {
            $artist->artistName = $this->body['artistName'];
        }
        if ($this->body['email']) {
            $artist->email = $this->body['email'];
        }
        if ($this->body['roles']) {
            $artist->roles = $this->body['roles'];
        }
        $artist->update();

        return $this->noContent();
    }
}
