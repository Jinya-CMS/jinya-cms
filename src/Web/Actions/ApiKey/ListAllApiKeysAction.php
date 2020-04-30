<?php

namespace App\Web\Actions\ApiKey;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllApiKeysAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        return $this->respondList($this->formatIterator(ApiKey::findByArtist($currentArtist->id)));
    }
}