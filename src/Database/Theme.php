<?php

namespace App\Database;

use App\Database\Strategies\JsonStrategy;
use App\Routing\Attributes\JinyaApi;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use stdClass;

/**
 * This class contains all information for themes that are stored in the database. Apart from the database side there are also filesystem theme information.
 */
#[JinyaApi(createEnabled: false, readEnabled: true, updateEnabled: false, deleteEnabled: false)]
class Theme extends Utils\LoadableEntity
{
    /** @var array<mixed> The stored configuration in the database */
    public array $configuration;
    /** @var string The theme description */
    public string $description;
    /** @var string The theme name */
    public string $name;
    /** @var string The theme display name */
    public string $displayName;
    /** @var array<string, string> A key-value store of SCSS variables changed by an artist */
    public array $scssVariables;

    /**
     * Gets the currently active theme
     *
     * @return Theme|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function getActiveTheme(): ?Theme
    {
        $sql = 'SELECT t.id AS id, configuration, description, name, display_name, scss_variables FROM theme t JOIN configuration c on t.id = c.current_frontend_theme_id';

        try {
            return self::getPdo()->fetchObject($sql, new self(), null, [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds the theme with matching ID
     *
     * @param int $id
     * @return Theme|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?Theme
    {
        return self::fetchSingleById(
            'theme',
            $id,
            new self(),
            [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]
        );
    }

    /**
     * Finds all themes matching the keyword
     *
     * @param string $keyword
     * @return Iterator<Theme>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, configuration, description, name, display_name, scss_variables FROM theme WHERE display_name LIKE :nameKeyword OR description LIKE :descKeyword';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['descKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"], [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds all themes
     *
     * @return Iterator<Theme>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator(
            'theme',
            new self(),
            [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]
        );
    }

    /**
     * Finds the theme with the given name
     *
     * @param string $name
     * @return Theme|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByName(string $name): ?Theme
    {
        $sql = 'SELECT id, configuration, description, name, display_name, scss_variables FROM theme WHERE name = :name';

        try {
            return self::getPdo()->fetchObject($sql, new self(), ['name' => $name],
                [
                    'scssVariables' => new JsonStrategy(),
                    'configuration' => new JsonStrategy(),
                ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Makes the current theme active
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function makeActiveTheme(): void
    {
        /** @noinspection SqlWithoutWhere */
        $sql = 'UPDATE configuration SET current_frontend_theme_id = :id';
        self::executeStatement($sql, ['id' => $this->id]);
    }

    /**
     * Gets all theme files
     *
     * @return array<string, File>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getFiles(): array
    {
        $result = [];
        $files = ThemeFile::findByTheme($this->getIdAsInt());
        /** @var ThemeFile $file */
        foreach ($files as $file) {
            $result[$file->name] = $file->getFile();
        }

        return $result;
    }

    /**
     * Gets all theme categories
     *
     * @return array<string, BlogCategory>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getCategories(): array
    {
        $result = [];
        $categories = ThemeBlogCategory::findByTheme($this->getIdAsInt());
        /** @var ThemeBlogCategory $category */
        foreach ($categories as $category) {
            $result[$category->name] = $category->getBlogCategory();
        }

        return $result;
    }

    /**
     * Gets all theme galleries
     *
     * @return array<string, Gallery>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getGalleries(): array
    {
        $result = [];
        $galleries = ThemeGallery::findByTheme($this->getIdAsInt());
        /** @var ThemeGallery $gallery */
        foreach ($galleries as $gallery) {
            $result[$gallery->name] = $gallery->getGallery();
        }

        return $result;
    }

    /**
     * Gets all theme forms
     *
     * @return array<string, Form>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getForms(): array
    {
        $result = [];
        $forms = ThemeForm::findByTheme($this->getIdAsInt());
        /** @var ThemeForm $form */
        foreach ($forms as $form) {
            $result[$form->name] = $form->getForm();
        }

        return $result;
    }

    /**
     * Gets all theme menus
     *
     * @return array<string, Menu>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getMenus(): array
    {
        $result = [];
        $menus = ThemeMenu::findByTheme($this->getIdAsInt());
        /** @var ThemeMenu $menu */
        foreach ($menus as $menu) {
            $result[$menu->name] = $menu->getMenu();
        }

        return $result;
    }

    /**
     * Gets all theme segment pages
     *
     * @return array<string, SegmentPage>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getSegmentPages(): array
    {
        $segmentPages = ThemeSegmentPage::findByTheme($this->getIdAsInt());
        $result = [];
        /** @var ThemeSegmentPage $segmentPage */
        foreach ($segmentPages as $segmentPage) {
            $result[$segmentPage->name] = $segmentPage->getSegmentPage();
        }

        return $result;
    }

    /**
     * Gets all theme pages
     *
     * @return array<string, SimplePage>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getPages(): array
    {
        $pages = ThemePage::findByTheme($this->getIdAsInt());
        $result = [];
        /** @var ThemePage $page */
        foreach ($pages as $page) {
            $result[$page->name] = $page->getPage();
        }

        return $result;
    }

    /**
     * Gets all theme assets
     *
     * @return array<string, ThemeAsset>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getAssets(): array
    {
        $assets = ThemeAsset::findByTheme($this->getIdAsInt());
        $result = [];
        /** @var ThemeAsset $asset */
        foreach ($assets as $asset) {
            $result[$asset->name] = $asset;
        }

        return $result;
    }

    /**
     * Creates the current theme
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalCreate(
            'theme',
            [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]
        );
    }

    /**
     * Deletes the current theme
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('theme');
    }

    /**
     * Updates the current theme
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate(
            'theme',
            [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]
        );
    }

    /**
     * Formats the theme into an array
     *
     * @return array<string, array<string, mixed>|int|stdClass|string>
     */
    #[Pure]
    #[ArrayShape([
        'configuration' => 'array|\stdClass',
        'description' => 'string',
        'name' => 'string',
        'displayName' => 'string',
        'scssVariables' => 'array|\stdClass',
        'id' => 'int'
    ])] public function format(): array
    {
        $scssVariables = $this->scssVariables;
        if (empty($scssVariables)) {
            $scssVariables = new stdClass();
        }
        $configuration = $this->configuration;
        if (empty($configuration)) {
            $configuration = new stdClass();
        }
        return [
            'configuration' => $configuration,
            'description' => $this->description,
            'name' => $this->name,
            'displayName' => $this->displayName,
            'scssVariables' => $scssVariables,
            'id' => $this->getIdAsInt(),
        ];
    }
}