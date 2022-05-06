<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Middleware\AuthenticationMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class UpdateColorScheme extends Action
{

    /**
     * @inheritDoc
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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