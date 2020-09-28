<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:11
 */

namespace Jinya\Services\Theme;

use Jinya\Entity\Theme\Theme;

interface ThemeServiceInterface
{
    /**
     * Gets the theme by the given name
     *
     * @return Theme
     */
    public function getTheme(string $name): ?Theme;

    /**
     * Gets the default theme
     */
    public function getDefaultJinyaTheme(): Theme;

    /**
     * Gets all themes
     *
     * @return Theme[]
     */
    public function getAllThemes(): array;

    /**
     * Registers the themes in twig
     */
    public function registerThemes(): void;

    /**
     * Gets the theme by name or creates a new instance
     */
    public function getThemeOrNewTheme(string $name): Theme;

    /**
     * Gets the directory where the themes are stored
     */
    public function getThemeDirectory(): string;

    /**
     * Updates the theme
     */
    public function update(Theme $theme): void;
}
