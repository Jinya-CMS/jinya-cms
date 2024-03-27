<?php

namespace App\Tests;

use App\Database\Theme;
use App\Theming\ThemeSyncer;
use Jinya\Database\Exception\NotNullViolationException;

class ThemeActionTestCase extends DatabaseAwareTestCase
{
    /**
     * @return Theme|null
     * @throws NotNullViolationException
     */
    public function getDefaultTheme(): ?Theme
    {
        $syncer = new ThemeSyncer();
        $syncer->syncThemes();

        return Theme::findByName('jinya-default-theme');
    }
}
