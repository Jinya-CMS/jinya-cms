<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 21:54
 */

namespace Jinya\Formatter\Configuration;

use Jinya\Entity\Configuration\Configuration;
use Jinya\Formatter\Theme\ThemeFormatterInterface;

class ConfigurationFormatter implements ConfigurationFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var Configuration */
    private $configuration;

    /** @var ThemeFormatterInterface */
    private $themeFormatter;

    /**
     * ConfigurationFormatter constructor.
     * @param ThemeFormatterInterface $themeFormatter
     */
    public function __construct(ThemeFormatterInterface $themeFormatter)
    {
        $this->themeFormatter = $themeFormatter;
    }

    /**
     * Initializes the formatter
     *
     * @param Configuration $configuration
     * @return ConfigurationFormatterInterface
     */
    public function init(Configuration $configuration): ConfigurationFormatterInterface
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Formats the frontend theme
     *
     * @return ConfigurationFormatterInterface
     */
    public function theme(): ConfigurationFormatterInterface
    {
        $this->formattedData['frontend']['theme'] = $this->themeFormatter
            ->init($this->configuration->getCurrentTheme())
            ->name()
            ->displayName()
            ->description()
            ->format();

        return $this;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }
}
