<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 07:59.
 */

namespace Jinya\Formatter\Theme;

use Jinya\Entity\Theme;
use Jinya\Formatter\FormatterInterface;

interface ThemeFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter.
     *
     * @param Theme $theme
     *
     * @return ThemeFormatterInterface
     */
    public function init(Theme $theme): ThemeFormatterInterface;

    /**
     * Formats the preview image.
     *
     * @return ThemeFormatterInterface
     */
    public function previewImage(): ThemeFormatterInterface;

    /**
     * Formats the config.
     *
     * @return ThemeFormatterInterface
     */
    public function config(): ThemeFormatterInterface;

    /**
     * Formats the description.
     *
     * @return ThemeFormatterInterface
     */
    public function description(): ThemeFormatterInterface;

    /**
     * Formats the name.
     *
     * @return ThemeFormatterInterface
     */
    public function name(): ThemeFormatterInterface;

    /**
     * Formats the display name.
     *
     * @return ThemeFormatterInterface
     */
    public function displayName(): ThemeFormatterInterface;

    /**
     * Formats the SCSS variables.
     *
     * @return ThemeFormatterInterface
     */
    public function scssVariables(): ThemeFormatterInterface;

    /**
     * Formats the primary menu.
     *
     * @return ThemeFormatterInterface
     */
    public function primaryMenu(): ThemeFormatterInterface;

    /**
     * Formats the secondary menu.
     *
     * @return ThemeFormatterInterface
     */
    public function secondaryMenu(): ThemeFormatterInterface;

    /**
     * Formats the footer menu.
     *
     * @return ThemeFormatterInterface
     */
    public function footerMenu(): ThemeFormatterInterface;
}
