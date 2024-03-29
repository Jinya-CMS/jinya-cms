<?php

namespace App\Theming\Extensions;

use App\Database\BlogCategory;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\ModernPage;
use App\Database\ClassicPage;
use App\Database\Theme;
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\BaseExtension;

/**
 * Provides extensions to the Plates engine, adding helper methods for handling of configuration links
 */
class LinksExtension extends BaseExtension
{
    /** @var Theme The theme from the database */
    private Theme $dbTheme;

    /**
     * Creates a new LinksExtension class
     *
     * @param Theme $dbTheme
     */
    public function __construct(Theme $dbTheme)
    {
        $this->dbTheme = $dbTheme;
    }

    /**
     * Registers the helper method with the plates engine
     *
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
    {
        $engine->functions->add('segmentPage', [$this, 'segmentPage']);
        $engine->functions->add('page', [$this, 'simplePage']);
        $engine->functions->add('simplePage', [$this, 'simplePage']);
        $engine->functions->add('file', [$this, 'file']);
        $engine->functions->add('menu', [$this, 'menu']);
        $engine->functions->add('gallery', [$this, 'gallery']);
        $engine->functions->add('form', [$this, 'form']);
        $engine->functions->add('blogCategory', [$this, 'blogCategory']);

        $engine->functions->add('hasSegmentPage', [$this, 'hasSegmentPage']);
        $engine->functions->add('hasPage', [$this, 'hasSimplePage']);
        $engine->functions->add('hasSimplePage', [$this, 'hasSimplePage']);
        $engine->functions->add('hasFile', [$this, 'hasFile']);
        $engine->functions->add('hasGallery', [$this, 'hasGallery']);
        $engine->functions->add('hasMenu', [$this, 'hasMenu']);
        $engine->functions->add('hasForm', [$this, 'hasForm']);
        $engine->functions->add('hasBlogCategory', [$this, 'hasBlogCategory']);
    }

    /**
     * Returns true when a segment page with that name is configured
     *
     * @param string $name
     * @return bool
     */
    public function hasSegmentPage(string $name): bool
    {
        return isset($this->dbTheme->getModernPages()[$name]);
    }

    /**
     * Returns true when a simple page with that name is configured
     *
     * @param string $name
     * @return bool
     */
    public function hasSimplePage(string $name): bool
    {
        return isset($this->dbTheme->getClassicPages()[$name]);
    }

    /**
     * Returns true when a file with that name is configured
     *
     * @param string $name
     * @return bool
     */
    public function hasFile(string $name): bool
    {
        return isset($this->dbTheme->getFiles()[$name]);
    }

    /**
     * Returns true when a gallery with that name is configured
     *
     * @param string $name
     * @return bool
     */
    public function hasGallery(string $name): bool
    {
        return isset($this->dbTheme->getGalleries()[$name]);
    }

    /**
     * Returns true when a menu with that name is configured
     *
     * @param string $name
     * @return bool
     */
    public function hasMenu(string $name): bool
    {
        return isset($this->dbTheme->getMenus()[$name]);
    }

    /**
     * Returns true when a form with that name is configured
     *
     * @param string $name
     * @return bool
     */
    public function hasForm(string $name): bool
    {
        return isset($this->dbTheme->getForms()[$name]);
    }

    /**
     * Returns true when a blog category with that name is configured
     *
     * @param string $name
     * @return bool
     */
    public function hasBlogCategory(string $name): bool
    {
        return isset($this->dbTheme->getCategories()[$name]);
    }

    /**
     * Returns the segment page with the given name or null
     *
     * @param string $name
     * @return ModernPage|null
     */
    public function segmentPage(string $name): ?ModernPage
    {
        return $this->dbTheme->getModernPages()[$name] ?? null;
    }

    /**
     * Returns the simple page with the given name or null
     *
     * @param string $name
     * @return ClassicPage|null
     */
    public function simplePage(string $name): ?ClassicPage
    {
        return $this->dbTheme->getClassicPages()[$name] ?? null;
    }

    /**
     * Returns the file with the given name or null
     *
     * @param string $name
     * @return File|null
     */
    public function file(string $name): ?File
    {
        return $this->dbTheme->getFiles()[$name] ?? null;
    }

    /**
     * Returns the form with the given name or null
     *
     * @param string $name
     * @return Form|null
     */
    public function form(string $name): ?Form
    {
        return $this->dbTheme->getForms()[$name] ?? null;
    }

    /**
     * Returns the menu with the given name or null
     *
     * @param string $name
     * @return Menu|null
     */
    public function menu(string $name): ?Menu
    {
        return $this->dbTheme->getMenus()[$name] ?? null;
    }

    /**
     * Returns the blog category with the given name or null
     *
     * @param string $name
     * @return BlogCategory|null
     */
    public function blogCategory(string $name): ?BlogCategory
    {
        return $this->dbTheme->getCategories()[$name] ?? null;
    }

    /**
     * Returns the gallery with the given name or null
     *
     * @param string $name
     * @return Gallery|null
     */
    public function gallery(string $name): ?Gallery
    {
        return $this->dbTheme->getGalleries()[$name] ?? null;
    }
}
