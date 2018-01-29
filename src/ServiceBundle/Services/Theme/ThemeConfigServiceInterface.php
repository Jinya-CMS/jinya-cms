<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 08:10
 */

namespace ServiceBundle\Services\Theme;


use DataBundle\Entity\Theme;

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
     * Gets the directory where the themes are stored
     *
     * @return string
     */
    public function getThemeDirectory(): string;

    /**
     * Saves the given theme configuration
     *
     * @param array $config
     * @param string $themeName
     */
    public function saveConfig(array $config, string $themeName): void;

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

    /**
     * Gets the forms for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getForms(string $name): array;
}