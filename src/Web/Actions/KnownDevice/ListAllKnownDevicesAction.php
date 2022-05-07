<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\KnownDevice;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthorizationMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class ListAllKnownDevicesAction extends Action
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
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST);

        return $this->respondList($this->formatIterator(KnownDevice::findByArtist($currentArtist->getIdAsInt())));
    }
}
