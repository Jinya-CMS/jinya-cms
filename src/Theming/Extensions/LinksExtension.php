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
use League\Plates\Extension\ExtensionInterface;

/**
 *
 */
class LinksExtension implements ExtensionInterface
{

    private Theme $dbTheme;

    /**
     * @param Theme $dbTheme
     */
    public function __construct(Theme $dbTheme)
    {
        $this->dbTheme = $dbTheme;
    }

    /**
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