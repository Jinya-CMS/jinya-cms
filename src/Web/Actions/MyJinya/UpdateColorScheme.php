<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Artist;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateColorScheme extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $colorScheme = $this->body['colorScheme'];
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        if ($colorScheme === 'light') {
            $currentArtist->prefersColorScheme = false;
        } elseif ($colorScheme === 'dark') {
            $currentArtist->prefersColorScheme = true;
        } else {
            $currentArtist->prefersColorScheme = null;
        }

        $currentArtist->update();

        return $this->noContent();
    }
}