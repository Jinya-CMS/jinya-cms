<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\Artist;
use App\Database\KnownDevice;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllKnownDevicesAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        return $this->respondList($this->formatIterator(KnownDevice::findByArtist($currentArtist->id)));
    }
}