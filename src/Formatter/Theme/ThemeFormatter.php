<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 08:00
 */

namespace Jinya\Formatter\Theme;

use Jinya\Entity\Theme\Theme;
use Jinya\Entity\Theme\ThemeArtGallery;
use Jinya\Entity\Theme\ThemeArtwork;
use Jinya\Entity\Theme\ThemeForm;
use Jinya\Entity\Theme\ThemeMenu;
use Jinya\Entity\Theme\ThemePage;
use Jinya\Entity\Theme\ThemeVideoGallery;
use Jinya\Formatter\Artwork\ArtworkFormatterInterface;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Formatter\Gallery\ArtGalleryFormatterInterface;
use Jinya\Formatter\Gallery\VideoGalleryFormatterInterface;
use Jinya\Formatter\Menu\MenuFormatterInterface;
use Jinya\Formatter\Page\PageFormatterInterface;
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

    /** @var ArtworkFormatterInterface */
    private $artworkFormatter;

    /** @var ArtGalleryFormatterInterface */
    private $artGalleryFormatter;

    /** @var VideoGalleryFormatterInterface */
    private $videoGalleryFormatter;

    /** @var PageFormatterInterface */
    private $pageFormatter;

    /** @var FormFormatterInterface */
    private $formFormatter;

    /**
     * ThemeFormatter constructor.
     * @param MenuFormatterInterface $menuFormatter
     * @param UrlGeneratorInterface $urlGenerator
     * @param ArtworkFormatterInterface $artworkFormatter
     * @param ArtGalleryFormatterInterface $artGalleryFormatter
     * @param VideoGalleryFormatterInterface $videoGalleryFormatter
     * @param PageFormatterInterface $pageFormatter
     * @param FormFormatterInterface $formFormatter
     */
    public function __construct(
        MenuFormatterInterface $menuFormatter,
        UrlGeneratorInterface $urlGenerator,
        ArtworkFormatterInterface $artworkFormatter,
        ArtGalleryFormatterInterface $artGalleryFormatter,
        VideoGalleryFormatterInterface $videoGalleryFormatter,
        PageFormatterInterface $pageFormatter,
        FormFormatterInterface $formFormatter
    ) {
        $this->menuFormatter = $menuFormatter;
        $this->urlGenerator = $urlGenerator;
        $this->artworkFormatter = $artworkFormatter;
        $this->artGalleryFormatter = $artGalleryFormatter;
        $this->videoGalleryFormatter = $videoGalleryFormatter;
        $this->pageFormatter = $pageFormatter;
        $this->formFormatter = $formFormatter;
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
     * Initializes the formatter
     *
     * @param Theme $theme
     * @return ThemeFormatterInterface
     */
    public function init(Theme $theme): ThemeFormatterInterface
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Formats the preview image
     *
     * @return ThemeFormatterInterface
     */
    public function previewImage(): ThemeFormatterInterface
    {
        $this->formattedData['previewImage'] = $this->urlGenerator->generate(
            'api_theme_preview_get',
            ['name' => $this->theme->getName()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this;
    }

    /**
     * Formats the config
     *
     * @return ThemeFormatterInterface
     */
    public function config(): ThemeFormatterInterface
    {
        $this->formattedData['config'] = $this->theme->getConfiguration();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return ThemeFormatterInterface
     */
    public function description(): ThemeFormatterInterface
    {
        $this->formattedData['description'] = $this->theme->getDescription();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return ThemeFormatterInterface
     */
    public function name(): ThemeFormatterInterface
    {
        $this->formattedData['name'] = $this->theme->getName();

        return $this;
    }

    /**
     * Formats the display name
     *
     * @return ThemeFormatterInterface
     */
    public function displayName(): ThemeFormatterInterface
    {
        $this->formattedData['displayName'] = $this->theme->getDisplayName();

        return $this;
    }

    /**
     * Formats the SCSS variables
     *
     * @return ThemeFormatterInterface
     */
    public function scssVariables(): ThemeFormatterInterface
    {
        $this->formattedData['scssVariables'] = $this->theme->getScssVariables();

        return $this;
    }

    /**
     * Formats the primary menu
     *
     * @return ThemeFormatterInterface
     */
    public function primaryMenu(): ThemeFormatterInterface
    {
        if (!array_key_exists('menu', $this->formattedData)) {
            $this->formattedData['menu'] = [];
        }

        $this->formattedData['menu']['primary'] = $this->theme->getPrimaryMenu() instanceof int
            ? $this->menuFormatter->init($this->theme->getPrimaryMenu())->id()->name()->format()
            : [];

        return $this;
    }

    /**
     * Formats the secondary menu
     *
     * @return ThemeFormatterInterface
     */
    public function secondaryMenu(): ThemeFormatterInterface
    {
        if (!array_key_exists('menu', $this->formattedData)) {
            $this->formattedData['menu'] = [];
        }

        $this->formattedData['menu']['secondary'] = $this->theme->getSecondaryMenu() instanceof int
            ? $this->menuFormatter->init($this->theme->getSecondaryMenu())->id()->name()->format()
            : [];

        return $this;
    }

    /**
     * Formats the footer menu
     *
     * @return ThemeFormatterInterface
     */
    public function footerMenu(): ThemeFormatterInterface
    {
        if (!array_key_exists('menu', $this->formattedData)) {
            $this->formattedData['menu'] = [];
        }

        $this->formattedData['menu']['footer'] = $this->theme->getFooterMenu() instanceof int
            ? $this->menuFormatter->init($this->theme->getFooterMenu())->id()->name()->format()
            : [];

        return $this;
    }

    /**
     * Formats the menus
     *
     * @return ThemeFormatterInterface
     */
    public function menus(): ThemeFormatterInterface
    {
        // TODO Refactor using key
        $this->formattedData['menus'] = $this->theme->getMenus()->map(function (ThemeMenu $menu) {
            return $this->menuFormatter->init($menu->getMenu())->id()->name()->format();
        })->getValues();

        return $this;
    }

    /**
     * Formats the video galleries
     *
     * @return ThemeFormatterInterface
     */
    public function videoGalleries(): ThemeFormatterInterface
    {
        // TODO Refactor using key
        $this->formattedData['videoGalleries'] = $this->theme->getVideoGalleries()
            ->map(function (ThemeVideoGallery $gallery) {
                return $this->videoGalleryFormatter->init($gallery->getVideoGallery())->id()->name()->format();
            })->getValues();

        return $this;
    }

    /**
     * Formats the pages
     *
     * @return ThemeFormatterInterface
     */
    public function pages(): ThemeFormatterInterface
    {
        // TODO Refactor using key
        $this->formattedData['pages'] = $this->theme->getPages()->map(function (ThemePage $page) {
            return $this->pageFormatter->init($page->getPage())->id()->name()->format();
        })->getValues();

        return $this;
    }

    /**
     * Formats the forms
     *
     * @return ThemeFormatterInterface
     */
    public function forms(): ThemeFormatterInterface
    {
        // TODO Refactor using key
        $this->formattedData['forms'] = $this->theme->getForms()->map(function (ThemeForm $form) {
            return $this->formFormatter->init($form->getForm())->id()->name()->format();
        })->getValues();

        return $this;
    }

    /**
     * Formats the artworks
     *
     * @return ThemeFormatterInterface
     */
    public function artworks(): ThemeFormatterInterface
    {
        // TODO Refactor using key
        $this->formattedData['artworks'] = $this->theme->getArtworks()->map(function (ThemeArtwork $artwork) {
            return $this->artworkFormatter->init($artwork->getArtwork())->id()->name()->format();
        })->getValues();

        return $this;
    }

    /**
     * Formats the links
     *
     * @return ThemeFormatterInterface
     */
    public function links(): ThemeFormatterInterface
    {
        return $this->artGalleries()->artworks()->videoGalleries()->pages()->forms()->menus();
    }

    /**
     * Formats the art galleries
     *
     * @return ThemeFormatterInterface
     */
    public function artGalleries(): ThemeFormatterInterface
    {
        // TODO Refactor using key
        $this->formattedData['artGalleries'] = $this
            ->theme
            ->getArtGalleries()
            ->map(function (ThemeArtGallery $gallery) {
                return $this->artGalleryFormatter->init($gallery->getArtGallery())->id()->name()->format();
            })->getValues();

        return $this;
    }
}
