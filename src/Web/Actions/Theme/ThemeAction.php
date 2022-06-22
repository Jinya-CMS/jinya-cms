<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Theming\ThemeSyncer;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 * The base theme action
 */
abstract class ThemeAction extends Action
{
    /**
     * Syncs the themes using the ThemeSyncer
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected function syncThemes(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();
    }
}
