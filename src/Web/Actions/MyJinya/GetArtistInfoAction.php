<?php

namespace App\Web\Actions\MyJinya;

use App\Web\Actions\Action;
use App\Web\Middleware\AuthorizationMiddleware;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get info about current artist
 */
class GetArtistInfoAction extends Action
{
    /**
     * Gets the information of the current artist
     *
     * @return Response
     */
    protected function action(): Response
    {
        $currentArtist = $this->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST);

        return $this->respond($currentArtist->format());
    }
}
