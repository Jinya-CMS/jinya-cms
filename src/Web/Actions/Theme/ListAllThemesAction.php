<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllThemesAction extends ThemeAction
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        return $this->respondList($this->formatIterator(Theme::findAll()));
    }
}