<?php

namespace App\Tests;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Theming\ThemeSyncer;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use PHPUnit\Framework\TestCase;

class ThemeActionTestCase extends TestCase
{
    /**
     * @return Theme|null
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getDefaultTheme(): ?Theme
    {
        $syncer = new ThemeSyncer();
        $syncer->syncThemes();

        return Theme::findByName('jinya-default-theme');
    }
}