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
    private array $formattedData;

    /** @var Configuration */
    private Configuration $configuration;

    /** @var ThemeFormatterInterface */
    private ThemeFormatterInterface $themeFormatter;

    /**
     * ConfigurationFormatter constructor.
     */
    public function __construct(ThemeFormatterInterface $themeFormatter)
    {
        $this->themeFormatter = $themeFormatter;
    }

    /**
     * Initializes the formatter
     */
    public function init(Configuration $configuration): ConfigurationFormatterInterface
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Formats the frontend theme
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

    /**
     * Formats the seconds the api key is valid
     */
    public function invalidateApiKeyAfter(): ConfigurationFormatterInterface
    {
        $this->formattedData['invalidateApiKeyAfter'] = $this->configuration->getInvalidateApiKeyAfter();

        return $this;
    }

    /**
     * Formats whether the messaging center is enabled
     */
    public function messagingCenterEnabled(): ConfigurationFormatterInterface
    {
        $this->formattedData['messagingCenterEnabled'] = $this->configuration->isMessagingCenterEnabled();

        return $this;
    }
}
