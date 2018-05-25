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
    public function init(Theme $theme): self;

    /**
     * Formats the preview image.
     *
     * @return ThemeFormatterInterface
     */
    public function previewImage(): self;

    /**
     * Formats the config.
     *
     * @return ThemeFormatterInterface
     */
    public function config(): self;

    /**
     * Formats the description.
     *
     * @return ThemeFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the name.
     *
     * @return ThemeFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the display name.
     *
     * @return ThemeFormatterInterface
     */
    public function displayName(): self;

    /**
     * Formats the SCSS variables.
     *
     * @return ThemeFormatterInterface
     */
    public function scssVariables(): self;

    /**
     * Formats the primary menu.
     *
     * @return ThemeFormatterInterface
     */
    public function primaryMenu(): self;

    /**
     * Formats the secondary menu.
     *
     * @return ThemeFormatterInterface
     */
    public function secondaryMenu(): self;

    /**
     * Formats the footer menu.
     *
     * @return ThemeFormatterInterface
     */
    public function footerMenu(): self;
}
