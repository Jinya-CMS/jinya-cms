<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/user/{id}', JinyaAction::GET)]
#[Authenticated(role: Authenticated::ADMIN)]
class GetArtistByIdAction extends Action
{
    protected function action(): Response
    {
        $artist = Artist::findById((int) $this->args['id']);
        if (null === $artist) {
            throw new HttpNotFoundException($this->request, 'Artist not found');
        }

        return $this->respond($artist->format(true));
    }
}
