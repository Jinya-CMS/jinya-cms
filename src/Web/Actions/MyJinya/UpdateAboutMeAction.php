<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthorizationMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to update the about me section the current artist
 */
class UpdateAboutMeAction extends Action
{
    /**
     * Updates the about me data of the artist, currently contains email, artist name and about me info
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST);

        if (isset($this->body['email'])) {
            $currentArtist->email = $this->body['email'];
        }

        if (isset($this->body['artistName'])) {
            $currentArtist->artistName = $this->body['artistName'];
        }

        if (isset($this->body['aboutMe'])) {
            $currentArtist->aboutMe = $this->body['aboutMe'];
        }

        $currentArtist->update();

        return $this->noContent();
    }
}
