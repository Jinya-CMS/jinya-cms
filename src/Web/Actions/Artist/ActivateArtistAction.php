<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}/activation', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::ADMIN)]
class ActivateArtistAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $artist = Artist::findById($this->args['id']);
        $artist->enabled = true;
        $artist->update();

        return $this->noContent();
    }
}
