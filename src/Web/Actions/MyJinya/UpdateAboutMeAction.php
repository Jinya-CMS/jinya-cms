<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Middleware\AuthenticationMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/me', JinyaAction::PUT)]
#[Authenticated]
class UpdateAboutMeAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
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
