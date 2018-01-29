<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:11
 */

namespace ServiceBundle\Services\Theme;


use DataBundle\Entity\Theme;

interface ThemeServiceInterface
{
    /**
     * Synchronizes the themes from the filesystem
     */
    public function syncThemes(): void;

    /**
     * Gets the theme by the given name
     *
     * @param string $name
     * @return Theme
     */
    public function getTheme(string $name): Theme;

    /**
     * Gets the default theme
     *
     * @return Theme
     */
    public function getDefaultJinyaTheme(): Theme;

    /**
     * Gets all themes
     *
     * @return Theme[]
     */
    public function getAllThemes(): array;

    /**
     * Gets the currently active theme
     *
     * @return Theme
     */
    public function getActiveTheme(): Theme;

    /**
     * Registers the themes in twig
     */
    public function registerThemes(): void;

    /**
     * Gets the theme by name or creates a new instance
     *
     * @param string $name
     * @return Theme
     */
    function getThemeOrNewTheme(string $name): Theme;
}