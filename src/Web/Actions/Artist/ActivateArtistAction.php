<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to activate an artist
 */
class ActivateArtistAction extends Action
{
    /**
     * Activates the given artist
     *
     * @return Response
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
        $artist->enabled = true;
        $artist->update();

        return $this->noContent();
    }
}
