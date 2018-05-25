<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:11.
 */

namespace Jinya\Services\Theme;

use Jinya\Entity\Theme;

interface ThemeServiceInterface
{
    /**
     * Gets the theme by the given name.
     *
     * @param string $name
     *
     * @return Theme
     */
    public function getTheme(string $name): Theme;

    /**
     * Gets the default theme.
     *
     * @return Theme
     */
    public function getDefaultJinyaTheme(): Theme;

    /**
     * Gets all themes.
     *
     * @return Theme[]
     */
    public function getAllThemes(): array;

    /**
     * Registers the themes in twig.
     */
    public function registerThemes(): void;

    /**
     * Gets the theme by name or creates a new instance.
     *
     * @param string $name
     *
     * @return Theme
     */
    public function getThemeOrNewTheme(string $name): Theme;

    /**
     * Gets the directory where the themes are stored.
     *
     * @return string
     */
    public function getThemeDirectory(): string;
}
