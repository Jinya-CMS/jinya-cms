<?php

namespace App\Web\Actions\MyJinya;

use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/account', JinyaAction::GET)]
#[JinyaAction('/api/me', JinyaAction::GET)]
#[Authenticated]
class GetArtistInfoAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     */
    protected function action(): Response
    {
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        return $this->respond($currentArtist->format(true));
    }
}
