<?php

namespace App\Theming;

use App\Database;

class ThemeSyncer
{
    public const THEME_BASE_PATH = __DIR__ . '/../../themes/';

    /**
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     */
    public function syncThemes(): void
    {
        $allThemes = iterator_to_array(Database\Theme::findAll());
        $themes = array_filter(
            array_diff(scandir(self::THEME_BASE_PATH), ['..', '.']),
            static fn($item) => is_dir(self::THEME_BASE_PATH . $item) && is_file(
                    self::THEME_BASE_PATH . "$item/theme.php"
                )
        );
        foreach ($themes as $dir) {
            $name = $dir;
            $dir = self::THEME_BASE_PATH . $dir;
            /** @noinspection PhpIncludeInspection */
            $config = require "$dir/theme.php";
            if (count(array_filter($allThemes, static fn(Database\Theme $theme) => $theme->name === $name)) === 0) {
                $dbTheme = new Database\Theme();
                $dbTheme->configuration = [];
                $dbTheme->scssVariables = [];
                $dbTheme->name = $name;
                $dbTheme->displayName = $config['displayName'] ?? $name;
                $dbTheme->description = $config['description'] ?? '';
                $dbTheme->create();
            }
        }

        $nonExistingThemes = array_filter(
            $allThemes,
            static fn(Database\Theme $theme) => !in_array($theme->name, $themes, true)
        );

        $activeTheme = Database\Theme::getActiveTheme();
        $defaultTheme = Database\Theme::findByName('jinya-default-theme');
        foreach ($nonExistingThemes as $nonExistingTheme) {
            if ($activeTheme === null || $nonExistingTheme->name === $activeTheme->name) {
                /** @noinspection NullPointerExceptionInspection */
                $defaultTheme->makeActiveTheme();
            }
            $nonExistingTheme->delete();
        }

        if ($activeTheme === null) {
            $defaultTheme->makeActiveTheme();
        }
    }
}