<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 21:52
 */

namespace Jinya\Formatter\Configuration;

use Jinya\Entity\Configuration\Configuration;
use Jinya\Formatter\FormatterInterface;

interface ConfigurationFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @param \Jinya\Entity\Configuration\Configuration $configuration
     * @return ConfigurationFormatterInterface
     */
    public function init(Configuration $configuration): self;

    /**
     * Formats the current theme
     *
     * @return ConfigurationFormatterInterface
     */
    public function theme(): self;
}
