<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthorizationMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to change the color scheme of the artist
 */
class UpdateColorSchemeAction extends Action
{
    /**
     * Updates the color scheme to the new value
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $colorScheme = $this->body['colorScheme'];
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST);

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
