<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Artist;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateAboutMeAction extends Action
{
    /**
     * @inheritDoc
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        if (isset($body['email'])) {
            $currentArtist->email = $body['email'];
        }

        if (isset($body['artistName'])) {
            $currentArtist->artistName = $body['artistName'];
        }

        if (isset($body['aboutMe'])) {
            $currentArtist->aboutMe = $body['aboutMe'];
        }

        $currentArtist->update();

        return $this->noContent();
    }
}