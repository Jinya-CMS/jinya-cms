<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 08:10
 */

namespace Jinya\Services\Theme;

use Jinya\Entity\Theme;

interface ThemeConfigServiceInterface
{
    /**
     * Gets the configuration for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getThemeConfig(string $name): array;

    /**
     * Gets the path for the styles
     *
     * @param Theme $theme
     * @return string
     */
    public function getStylesPath(Theme $theme): string;

    /**
     * Gets the config form for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getConfigForm(string $name): array;

    /**
     * Gets the namespace for the given theme
     *
     * @param Theme $theme
     * @return string
     */
    public function getThemeNamespace(Theme $theme): string;

    /**
     * Saves the given theme configuration
     *
     * @param string $themeName
     * @param array $config
     * @param bool $override
     */
    public function saveConfig(string $themeName, array $config, bool $override = true): void;

    /**
     * Gets the variables for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getVariablesForm(string $name): array;

    /**
     * Saves the given variables
     *
     * @param string $name
     * @param array $variables
     */
    public function setVariables(string $name, array $variables): void;

    /**
     * Sets the menus for the given theme
     *
     * @param string $name
     * @param array $menus
     */
    public function setMenus(string $name, array $menus): void;

    /**
     * Gets the forms for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getForms(string $name): array;

    /**
     * Resets the given themes configuration
     *
     * @param string $name
     */
    public function resetConfig(string $name): void;

    /**
     * Resets the given themes variables
     *
     * @param string $name
     */
    public function resetVariables(string $name): void;

    /**
     * Removes the given file
     *
     * @param string $name
     * @param string $key
     */
    public function removeFile(string $name, string $key): void;
}
