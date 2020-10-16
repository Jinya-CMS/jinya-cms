<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 08:10
 */

namespace Jinya\Services\Theme;

use Jinya\Entity\Theme\Theme;

interface ThemeConfigServiceInterface
{
    /**
     * Gets the configuration for the given theme
     */
    public function getThemeConfig(string $name): array;

    /**
     * Gets the path for the styles
     */
    public function getStylesPath(Theme $theme): string;

    /**
     * Gets the config form for the given theme
     */
    public function getConfigForm(string $name): array;

    /**
     * Gets the namespace for the given theme
     */
    public function getThemeNamespace(Theme $theme): string;

    /**
     * Saves the given theme configuration
     */
    public function saveConfig(string $themeName, array $config, bool $override = true): void;

    /**
     * Gets the variables for the given theme
     */
    public function getVariablesForm(string $name): array;

    /**
     * Saves the given variables
     */
    public function setVariables(string $name, array $variables): void;

    /**
     * Gets the forms for the given theme
     */
    public function getForms(string $name): array;

    /**
     * Resets the given themes configuration
     */
    public function resetConfig(string $name): void;

    /**
     * Resets the given themes variables
     */
    public function resetVariables(string $name): void;

    /**
     * Removes the given file
     */
    public function removeFile(string $name, string $key): void;
}
