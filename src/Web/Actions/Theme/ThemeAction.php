<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Theming\ThemeSyncer;
use App\Web\Actions\Action;

abstract class ThemeAction extends Action
{
    /**
     * @throws UniqueFailedException
     */
    protected function syncThemes(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();
    }
}