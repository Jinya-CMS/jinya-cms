<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to activate an artist
 */
class CreateArtistAction extends Action
{
    /**
     * Activates the given artist
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $artist = new Artist();
        $artist->setPassword($this->body['password']);
        $artist->artistName = $this->body['artistName'];
        $artist->email = $this->body['email'];
        $artist->roles = $this->body['roles'];
        $artist->enabled = true;
        $artist->create();

        return $this->respond($artist->format(), Action::HTTP_CREATED);
    }
}
