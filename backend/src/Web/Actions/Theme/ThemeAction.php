<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Theming\ThemeSyncer;
use App\Web\Actions\Action;

abstract class ThemeAction extends Action
{
    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function syncThemes(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();
    }
}