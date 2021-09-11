<?php

namespace App\Database;

use App\Database\Exceptions\InvalidQueryException;
use App\Database\Strategies\JsonStrategy;
use App\Database\Utils\FormattableEntityInterface;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use stdClass;

class Theme extends Utils\LoadableEntity implements FormattableEntityInterface
{

    public array $configuration;
    public string $description;
    public string $name;
    public string $displayName;
    public array $scssVariables;

    /**
     * Gets the currently active theme
     *
     * @return Theme|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function getActiveTheme(): ?Theme
    {
        $sql = 'SELECT t.id AS id, configuration, description, name, display_name, scss_variables FROM theme t JOIN configuration c on t.id = c.current_frontend_theme_id';

        $result = self::executeStatement($sql);

        if (count($result) === 0) {
            return null;
        }
        return self::hydrateSingleResult(
            $result[0],
            new self(),
            [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]
        );
    }

    /**
     * @inheritDoc
     * @return Theme
     */
    public static function findById(int $id): ?object
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
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, configuration, description, name, display_name, scss_variables FROM theme WHERE display_name LIKE :nameKeyword OR description LIKE :descKeyword';

        $result = self::executeStatement($sql, ['descKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"]);

        return self::hydrateMultipleResults(
            $result,
            new self(),
            [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray(
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByName(string $name): ?Theme
    {
        $sql = 'SELECT id, configuration, description, name, display_name, scss_variables FROM theme WHERE name = :name';

        $result = self::executeStatement($sql, ['name' => $name]);

        if (count($result) === 0) {
            return null;
        }
        return self::hydrateSingleResult(
            $result[0],
            new self(),
            [
                'scssVariables' => new JsonStrategy(),
                'configuration' => new JsonStrategy(),
            ]
        );
    }

    /**
     * Makes the current theme active
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
     * @return array (File|null)[]
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getFiles(): array
    {
        $result = [];
        $files = ThemeFile::findByTheme($this->id);
        /** @var ThemeFile $file */
        foreach ($files as $file) {
            $result[$file->name] = $file->getFile();
        }

        return $result;
    }

    /**
     * Gets all theme categories
     *
     * @return array<string, BlogCategory|null>
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
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
     * @return array (Gallery|null)[]
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getGalleries(): array
    {
        $result = [];
        $galleries = ThemeGallery::findByTheme($this->id);
        /** @var ThemeGallery $gallery */
        foreach ($galleries as $gallery) {
            $result[$gallery->name] = $gallery->getGallery();
        }

        return $result;
    }

    /**
     * Gets all theme forms
     *
     * @return array (Form|null)[]
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getForms(): array
    {
        $result = [];
        $forms = ThemeForm::findByTheme($this->id);
        /** @var ThemeForm $form */
        foreach ($forms as $form) {
            $result[$form->name] = $form->getForm();
        }

        return $result;
    }

    /**
     * Gets all theme menus
     *
     * @return array (Menu|null)[]
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getMenus(): array
    {
        $result = [];
        $menus = ThemeMenu::findByTheme($this->id);
        /** @var ThemeMenu $menu */
        foreach ($menus as $menu) {
            $result[$menu->name] = $menu->getMenu();
        }

        return $result;
    }

    /**
     * Gets all theme segmentPages
     *
     * @return array (SegmentPage|null)[]
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getSegmentPages(): array
    {
        $segmentPages = ThemeSegmentPage::findByTheme($this->id);
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
     * @return array (SimplePage|null)[]
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getPages(): array
    {
        $pages = ThemePage::findByTheme($this->id);
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
     * @return ThemeAsset[]
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getAssets(): array
    {
        $assets = ThemeAsset::findByTheme($this->id);
        $result = [];
        /** @var ThemeAsset $asset */
        foreach ($assets as $asset) {
            $result[$asset->name] = $asset;
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @throws Exception
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
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme');
    }

    /**
     * @inheritDoc
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

    #[Pure] #[ArrayShape([
        'configuration' => "array|\stdClass",
        'description' => "string",
        'name' => "string",
        'displayName' => "string",
        'scssVariables' => "array|\stdClass",
        'id' => "int"
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