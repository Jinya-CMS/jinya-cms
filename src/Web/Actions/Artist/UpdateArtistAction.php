<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateArtistAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $id = (int)$this->args['id'];
        $artist = Artist::findById($id);
        if ($artist === null) {
            throw new NoResultException($this->request, 'Artist not found');
        }
        $body = $this->request->getParsedBody();
        if (isset($body['email'])) {
            $artist->email = $body['email'];
        }
        if (isset($body['roles'])) {
            $artist->roles = $body['roles'];
        }
        if (isset($body['enabled'])) {
            $artist->enabled = $body['enabled'];
        }
        if (isset($body['artistName'])) {
            $artist->artistName = $body['artistName'];
        }
        if (isset($body['password'])) {
            $artist->setPassword($body['password']);
        }
        try {
            $artist->update();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Email exists');
        }

        return $this->noContent();
    }
}