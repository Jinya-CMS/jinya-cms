<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 07:59
 */

namespace Jinya\Formatter\Theme;

use Jinya\Entity\Theme\Theme;
use Jinya\Formatter\FormatterInterface;

interface ThemeFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @param Theme $theme
     * @return ThemeFormatterInterface
     */
    public function init(Theme $theme): self;

    /**
     * Formats the preview image
     *
     * @return ThemeFormatterInterface
     */
    public function previewImage(): self;

    /**
     * Formats the config
     *
     * @return ThemeFormatterInterface
     */
    public function config(): self;

    /**
     * Formats the description
     *
     * @return ThemeFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the name
     *
     * @return ThemeFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the display name
     *
     * @return ThemeFormatterInterface
     */
    public function displayName(): self;

    /**
     * Formats the SCSS variables
     *
     * @return ThemeFormatterInterface
     */
    public function scssVariables(): self;

    /**
     * Formats the primary menu
     *
     * @return ThemeFormatterInterface
     */
    public function primaryMenu(): self;

    /**
     * Formats the secondary menu
     *
     * @return ThemeFormatterInterface
     */
    public function secondaryMenu(): self;

    /**
     * Formats the footer menu
     *
     * @return ThemeFormatterInterface
     */
    public function footerMenu(): self;

    /**
     * Formats the menus
     *
     * @return ThemeFormatterInterface
     */
    public function menus(): self;

    /**
     * Formats the pages
     *
     * @return ThemeFormatterInterface
     */
    public function pages(): self;

    /**
     * Formats the segment pages
     *
     * @return ThemeFormatterInterface
     */
    public function segmentPages(): self;

    /**
     * Formats the forms
     *
     * @return ThemeFormatterInterface
     */
    public function forms(): self;

    /**
     * Formats the files
     *
     * @return ThemeFormatterInterface
     */
    public function files(): self;

    /**
     * Formats the artworks
     *
     * @return ThemeFormatterInterface
     */
    public function galleries(): self;

    /**
     * Formats the links
     *
     * @return ThemeFormatterInterface
     */
    public function links(): self;
}
