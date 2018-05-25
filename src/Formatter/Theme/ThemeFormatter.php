<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 08:00.
 */

namespace Jinya\Formatter\Theme;

use Jinya\Entity\Menu;
use Jinya\Entity\Theme;
use Jinya\Formatter\Menu\MenuFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function array_key_exists;

class ThemeFormatter implements ThemeFormatterInterface
{
    /** @var array */
    private $formattedData;
    /** @var Theme */
    private $theme;

    /** @var MenuFormatterInterface */
    private $menuFormatter;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * ThemeFormatter constructor.
     *
     * @param MenuFormatterInterface $menuFormatter
     * @param UrlGeneratorInterface  $urlGenerator
     */
    public function __construct(MenuFormatterInterface $menuFormatter, UrlGeneratorInterface $urlGenerator)
    {
        $this->menuFormatter = $menuFormatter;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array.
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatter.
     *
     * @param Theme $theme
     *
     * @return ThemeFormatterInterface
     */
    public function init(Theme $theme): ThemeFormatterInterface
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Formats the preview image.
     *
     * @return ThemeFormatterInterface
     */
    public function previewImage(): ThemeFormatterInterface
    {
        $this->formattedData['previewImage'] = $this->urlGenerator->generate('api_theme_preview_get', ['name' => $this->theme->getName()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this;
    }

    /**
     * Formats the config.
     *
     * @return ThemeFormatterInterface
     */
    public function config(): ThemeFormatterInterface
    {
        $this->formattedData['config'] = $this->theme->getConfiguration();

        return $this;
    }

    /**
     * Formats the description.
     *
     * @return ThemeFormatterInterface
     */
    public function description(): ThemeFormatterInterface
    {
        $this->formattedData['description'] = $this->theme->getDescription();

        return $this;
    }

    /**
     * Formats the name.
     *
     * @return ThemeFormatterInterface
     */
    public function name(): ThemeFormatterInterface
    {
        $this->formattedData['name'] = $this->theme->getName();

        return $this;
    }

    /**
     * Formats the display name.
     *
     * @return ThemeFormatterInterface
     */
    public function displayName(): ThemeFormatterInterface
    {
        $this->formattedData['displayName'] = $this->theme->getDisplayName();

        return $this;
    }

    /**
     * Formats the SCSS variables.
     *
     * @return ThemeFormatterInterface
     */
    public function scssVariables(): ThemeFormatterInterface
    {
        $this->formattedData['scssVariables'] = $this->theme->getScssVariables();

        return $this;
    }

    /**
     * Formats the primary menu.
     *
     * @return ThemeFormatterInterface
     */
    public function primaryMenu(): ThemeFormatterInterface
    {
        if (!array_key_exists('menu', $this->formattedData)) {
            $this->formattedData['menu'] = [];
        }

        $this->formattedData['menu']['primary'] = $this->theme->getPrimaryMenu() instanceof Menu ? $this->menuFormatter->init($this->theme->getPrimaryMenu())->id()->name()->format() : [];

        return $this;
    }

    /**
     * Formats the secondary menu.
     *
     * @return ThemeFormatterInterface
     */
    public function secondaryMenu(): ThemeFormatterInterface
    {
        if (!array_key_exists('menu', $this->formattedData)) {
            $this->formattedData['menu'] = [];
        }

        $this->formattedData['menu']['secondary'] = $this->theme->getSecondaryMenu() instanceof Menu ? $this->menuFormatter->init($this->theme->getSecondaryMenu())->id()->name()->format() : [];

        return $this;
    }

    /**
     * Formats the footer menu.
     *
     * @return ThemeFormatterInterface
     */
    public function footerMenu(): ThemeFormatterInterface
    {
        if (!array_key_exists('menu', $this->formattedData)) {
            $this->formattedData['menu'] = [];
        }

        $this->formattedData['menu']['footer'] = $this->theme->getFooterMenu() instanceof Menu ? $this->menuFormatter->init($this->theme->getFooterMenu())->id()->name()->format() : [];

        return $this;
    }
}
