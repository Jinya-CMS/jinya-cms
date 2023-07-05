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
        if (array_key_exists('password', $this->body)) {
            $artist->setPassword($this->body['password']);
        }
        if (array_key_exists('artistName', $this->body)) {
            $artist->artistName = $this->body['artistName'];
        }
        if (array_key_exists('email', $this->body)) {
            $artist->email = $this->body['email'];
        }
        if (array_key_exists('roles', $this->body)) {
            $artist->roles = $this->body['roles'];
        }
        $artist->update();

        return $this->noContent();
    }
}
