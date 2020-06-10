<?php

namespace App\Theming;

use App\Database;

class ThemeSyncer
{
    public const THEME_BASE_PATH = __DIR__ . '/../../themes/';


    public function syncThemes(): void
    {
        $dirs = scandir(self::THEME_BASE_PATH);
        foreach ($dirs as $dir) {
            $dir = self::THEME_BASE_PATH . $dir;
            if (is_dir($dir) && is_file($dir . '/theme.php')) {
                $dbTheme = new Database\Theme();
                $dbTheme->configuration = [];
                $dbTheme->scssVariables = [];
            }
        }
    }
}