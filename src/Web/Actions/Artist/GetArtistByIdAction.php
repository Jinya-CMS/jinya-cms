<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class GetArtistByIdAction extends Action
{
    protected function action(): Response
    {
        $artist = Artist::findById((int)$this->args['id']);
        if ($artist === null) {
            throw new HttpNotFoundException($this->request, 'Artist not found');
        }

        return $this->respond($artist->format());
    }
}