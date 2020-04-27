<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteArtistAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $artist = Artist::findById($id);
        if ($artist === null) {
            throw new NoResultException($this->request, 'Artist not found');
        }
        $artist->delete();

        return $this->noContent();
    }
}