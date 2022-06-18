<?php

namespace App\Web\Actions\MyJinya;

use App\Web\Actions\Action;
use App\Web\Middleware\AuthorizationMiddleware;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class GetArtistInfoAction extends Action
{
    /**
     * @inheritDoc
     * @return Response
     */
    protected function action(): Response
    {
        $currentArtist = $this->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST);

        return $this->respond($currentArtist->format());
    }
}
