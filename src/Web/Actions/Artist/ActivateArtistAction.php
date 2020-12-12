<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ActivateArtistAction extends Action
{
    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $artist = Artist::findById($this->args['id']);
        $artist->enabled = true;
        $artist->update();

        return $this->noContent();
    }
}