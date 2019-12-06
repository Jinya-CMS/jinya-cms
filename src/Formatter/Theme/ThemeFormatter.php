<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 08:00
 */

namespace Jinya\Formatter\Theme;

use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Theme\Theme;
use Jinya\Formatter\Artwork\ArtworkFormatterInterface;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Formatter\Gallery\ArtGalleryFormatterInterface;
use Jinya\Formatter\Gallery\VideoGalleryFormatterInterface;
use Jinya\Formatter\Media\FileFormatterInterface;
use Jinya\Formatter\Media\GalleryFormatterInterface;
use Jinya\Formatter\Menu\MenuFormatterInterface;
use Jinya\Formatter\Page\PageFormatterInterface;
use Jinya\Formatter\SegmentPage\SegmentPageFormatterInterface;
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

    /** @var GalleryFormatterInterface */
    private $galleryFormatter;

    /** @var FileFormatterInterface */
    private $fileFormatter;

    /** @var PageFormatterInterface */
    private $pageFormatter;

    /** @var SegmentPageFormatterInterface */
    private $segmentPageFormatter;

    /** @var FormFormatterInterface */
    private $formFormatter;

    /**
     * ThemeFormatter constructor.
     * @param MenuFormatterInterface $menuFormatter
     * @param UrlGeneratorInterface $urlGenerator
     * @param ArtworkFormatterInterface $artworkFormatter
     * @param ArtGalleryFormatterInterface $artGalleryFormatter
     * @param VideoGalleryFormatterInterface $videoGalleryFormatter
     * @param GalleryFormatterInterface $galleryFormatter
     * @param FileFormatterInterface $fileFormatter
     * @param PageFormatterInterface $pageFormatter
     * @param SegmentPageFormatterInterface $segmentPageFormatter
     * @param FormFormatterInterface $formFormatter
     */
    public function __construct(
        MenuFormatterInterface $menuFormatter,
        UrlGeneratorInterface $urlGenerator,
        ArtworkFormatterInterface $artworkFormatter,
        ArtGalleryFormatterInterface $artGalleryFormatter,
        VideoGalleryFormatterInterface $videoGalleryFormatter,
        GalleryFormatterInterface $galleryFormatter,
        FileFormatterInterface $fileFormatter,
        PageFormatterInterface $pageFormatter,
        SegmentPageFormatterInterface $segmentPageFormatter,
        FormFormatterInterface $formFormatter
    ) {
        $this->menuFormatter = $menuFormatter;
        $this->urlGenerator = $urlGenerator;
        $this->artworkFormatter = $artworkFormatter;
        $this->artGalleryFormatter = $artGalleryFormatter;
        $this->videoGalleryFormatter = $videoGalleryFormatter;
        $this->galleryFormatter = $galleryFormatter;
        $this->fileFormatter = $fileFormatter;
        $this->pageFormatter = $pageFormatter;
        $this->segmentPageFormatter = $segmentPageFormatter;
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

        $this->formattedData['menu']['primary'] = $this->theme->getPrimaryMenu() instanceof Menu
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

        /* @noinspection PhpUndefinedClassInspection */
        $this->formattedData['menu']['secondary'] = $this->theme->getSecondaryMenu() instanceof Menu
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

        /* @noinspection PhpUndefinedClassInspection */
        $this->formattedData['menu']['footer'] = $this->theme->getFooterMenu() instanceof Menu
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
        $this->formattedData['menus'] = [];
        foreach ($this->theme->getMenus() as $menu) {
            $this->formattedData['menus'][$menu->getName()] = $this
                ->menuFormatter
                ->init($menu->getMenu())
                ->id()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the video galleries
     *
     * @return ThemeFormatterInterface
     */
    public function videoGalleries(): ThemeFormatterInterface
    {
        $this->formattedData['videoGalleries'] = [];
        foreach ($this->theme->getVideoGalleries() as $videoGallery) {
            $this->formattedData['videoGalleries'][$videoGallery->getName()] = $this
                ->videoGalleryFormatter
                ->init($videoGallery->getVideoGallery())
                ->id()
                ->slug()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the pages
     *
     * @return ThemeFormatterInterface
     */
    public function pages(): ThemeFormatterInterface
    {
        $this->formattedData['pages'] = [];
        foreach ($this->theme->getPages() as $page) {
            $this->formattedData['pages'][$page->getName()] = $this
                ->pageFormatter
                ->init($page->getPage())
                ->id()
                ->slug()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the forms
     *
     * @return ThemeFormatterInterface
     */
    public function forms(): ThemeFormatterInterface
    {
        $this->formattedData['forms'] = [];
        foreach ($this->theme->getForms() as $form) {
            $this->formattedData['forms'][$form->getName()] = $this
                ->formFormatter
                ->init($form->getForm())
                ->id()
                ->slug()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the artworks
     *
     * @return ThemeFormatterInterface
     */
    public function artworks(): ThemeFormatterInterface
    {
        $this->formattedData['artworks'] = [];
        foreach ($this->theme->getArtworks() as $artwork) {
            $this->formattedData['artworks'][$artwork->getName()] = $this
                ->artworkFormatter
                ->init($artwork->getArtwork())
                ->id()
                ->slug()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the links
     *
     * @return ThemeFormatterInterface
     */
    public function links(): ThemeFormatterInterface
    {
        return $this
            ->artGalleries()
            ->artworks()
            ->videoGalleries()
            ->pages()
            ->forms()
            ->menus()
            ->galleries()
            ->files()
            ->segmentPages();
    }

    /**
     * Formats the art galleries
     *
     * @return ThemeFormatterInterface
     */
    public function artGalleries(): ThemeFormatterInterface
    {
        $this->formattedData['artGalleries'] = [];
        foreach ($this->theme->getArtGalleries() as $artGallery) {
            $this->formattedData['artGalleries'][$artGallery->getName()] = $this
                ->artGalleryFormatter
                ->init($artGallery->getArtGallery())
                ->id()
                ->slug()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the segment pages
     *
     * @return ThemeFormatterInterface
     */
    public function segmentPages(): ThemeFormatterInterface
    {
        $this->formattedData['segmentPages'] = [];
        foreach ($this->theme->getSegmentPages() as $segmentPage) {
            $this->formattedData['segmentPages'][$segmentPage->getName()] = $this
                ->segmentPageFormatter
                ->init($segmentPage->getSegmentPage())
                ->slug()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the files
     *
     * @return ThemeFormatterInterface
     */
    public function files(): ThemeFormatterInterface
    {
        $this->formattedData['files'] = [];
        foreach ($this->theme->getFiles() as $file) {
            $this->formattedData['files'][$file->getName()] = $this
                ->fileFormatter
                ->init($file->getFile())
                ->id()
                ->name()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the artworks
     *
     * @return ThemeFormatterInterface
     */
    public function galleries(): ThemeFormatterInterface
    {
        $this->formattedData['galleries'] = [];
        foreach ($this->theme->getGalleries() as $gallery) {
            $this->formattedData['galleries'][$gallery->getName()] = $this
                ->galleryFormatter
                ->init($gallery->getGallery())
                ->slug()
                ->name()
                ->format();
        }

        return $this;
    }
}
