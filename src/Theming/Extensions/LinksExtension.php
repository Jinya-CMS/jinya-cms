<?php

namespace App\Theming\Extensions;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\SegmentPage;
use App\Database\SimplePage;
use App\Database\Theme;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use League\Plates\Engine;
use League\Plates\Extension\BaseExtension;

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
        $engine->registerFunction('segmentPage', [$this, 'segmentPage']);
        $engine->registerFunction('page', [$this, 'simplePage']);
        $engine->registerFunction('simplePage', [$this, 'simplePage']);
        $engine->registerFunction('file', [$this, 'file']);
        $engine->registerFunction('menu', [$this, 'menu']);
        $engine->registerFunction('gallery', [$this, 'gallery']);
        $engine->registerFunction('form', [$this, 'form']);
        $engine->registerFunction('blogCategory', [$this, 'blogCategory']);

        $engine->registerFunction('hasSegmentPage', [$this, 'hasSegmentPage']);
        $engine->registerFunction('hasPage', [$this, 'hasSimplePage']);
        $engine->registerFunction('hasSimplePage', [$this, 'hasSimplePage']);
        $engine->registerFunction('hasFile', [$this, 'hasFile']);
        $engine->registerFunction('hasGallery', [$this, 'hasGallery']);
        $engine->registerFunction('hasMenu', [$this, 'hasMenu']);
        $engine->registerFunction('hasForm', [$this, 'hasForm']);
        $engine->registerFunction('hasBlogCategory', [$this, 'hasBlogCategory']);
    }

    /**
     * Returns true when a segment page with that name is configured
     *
     * @param string $name
     * @return bool
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function hasSegmentPage(string $name): bool
    {
        return isset($this->dbTheme->getSegmentPages()[$name]);
    }

    /**
     * Returns true when a simple page with that name is configured
     *
     * @param string $name
     * @return bool
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function hasSimplePage(string $name): bool
    {
        return isset($this->dbTheme->getPages()[$name]);
    }

    /**
     * Returns true when a file with that name is configured
     *
     * @param string $name
     * @return bool
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
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
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
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
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
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
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
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
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function hasBlogCategory(string $name): bool
    {
        return isset($this->dbTheme->getCategories()[$name]);
    }

    /**
     * Returns the segment page with the given name or null
     *
     * @param string $name
     * @return SegmentPage|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function segmentPage(string $name): ?SegmentPage
    {
        return $this->dbTheme->getSegmentPages()[$name] ?? null;
    }

    /**
     * Returns the simple page with the given name or null
     *
     * @param string $name
     * @return SimplePage|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function simplePage(string $name): ?SimplePage
    {
        return $this->dbTheme->getPages()[$name] ?? null;
    }

    /**
     * Returns the file with the given name or null
     *
     * @param string $name
     * @return File|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
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
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
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
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
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
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
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
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function gallery(string $name): ?Gallery
    {
        return $this->dbTheme->getGalleries()[$name] ?? null;
    }
}