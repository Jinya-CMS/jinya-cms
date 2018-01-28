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
     * Saves the given theme configuration
     *
     * @param array $config
     * @param string $themeName
     */
    public function saveConfig(array $config, string $themeName): void;

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
     * Gets the namespace for the given theme
     *
     * @param Theme $theme
     * @return string
     */
    public function getThemeNamespace(Theme $theme): string;

    /**
     * Gets the directory where the themes are stored
     *
     * @return string
     */
    public function getThemeDirectory(): string;

    /**
     * Registers the themes in twig
     */
    public function registerThemes(): void;

    /**
     * Compiles the scss and javascript of the given @see Theme
     *
     * @param Theme $theme
     */
    public function compileTheme(Theme $theme): void;

    /**
     * Checks whether the given theme is compiled
     *
     * @param Theme $theme
     * @return bool
     */
    public function isCompiled(Theme $theme): bool;

    /**
     * Gets the config form for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getConfigForm(string $name): array;

    /**
     * Gets the variables for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getVariables(string $name): array;

    /**
     * Saves the given variables
     *
     * @param string $name
     * @param array $variables
     * @return void
     */
    public function setVariables(string $name, array $variables): void;

    /**
     * Sets the menus for the given theme
     *
     * @param string $name
     * @param array $menus
     */
    public function setMenus(string $name, array $menus): void;
}