<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 21:52
 */

namespace Jinya\Formatter\Configuration;


use Jinya\Entity\Configuration;
use Jinya\Formatter\FormatterInterface;

interface ConfigurationFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @param Configuration $configuration
     * @return ConfigurationFormatterInterface
     */
    public function init(Configuration $configuration): ConfigurationFormatterInterface;

    /**
     * Formats the designer theme
     *
     * @return ConfigurationFormatterInterface
     */
    public function designerTheme(): ConfigurationFormatterInterface;

    /**
     * Formats the frontend theme
     *
     * @return ConfigurationFormatterInterface
     */
    public function frontendTheme(): ConfigurationFormatterInterface;
}