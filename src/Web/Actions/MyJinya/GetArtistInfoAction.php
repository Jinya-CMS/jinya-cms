<?php

namespace App\Web\Actions\MyJinya;

use App\Web\Actions\Action;
use App\Web\Middleware\AuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface as Response;

class GetArtistInfoAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     */
    protected function action(): Response
    {
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        return $this->respond($currentArtist->format());
    }
}
