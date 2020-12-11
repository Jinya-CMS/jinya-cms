<?php

namespace App\Database;

use App\Database\Strategies\JsonStrategy;
use App\Database\Utils\FormattableEntityInterface;
use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\Json;
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
     * @return Theme
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function getActiveTheme(): ?Theme
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('theme')
            ->join('configuration', 'configuration.current_frontend_theme_id = theme.id', []);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select));

        return self::hydrateSingleResult($result, new self(), [
            'scssVariables' => new JsonStrategy(),
            'configuration' => new JsonStrategy(),
        ]);
    }

    /**
     * @inheritDoc
     * @return Theme
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('theme', $id, new self(), [
            'scssVariables' => new JsonStrategy(),
            'configuration' => new JsonStrategy(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('theme')
            ->where(['display_name LIKE :keyword', 'description LIKE :keyword'], PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(), [
            'scssVariables' => new JsonStrategy(),
            'configuration' => new JsonStrategy(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('theme', new self(), [
            'scssVariables' => new JsonStrategy(),
            'configuration' => new JsonStrategy(),
        ]);
    }

    /**
     * Finds the theme with the given name
     *
     * @param string $name
     * @return Theme
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByName(string $name): ?Theme
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('theme')
            ->where('name = :name');

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['name' => $name]);

        return self::hydrateSingleResult($result, new self(), [
            'scssVariables' => new JsonStrategy(),
            'configuration' => new JsonStrategy(),
        ]);
    }

    /**
     * Makes the current theme active
     */
    public function makeActiveTheme(): void
    {
        $sql = self::getSql();
        $update = $sql->update('configuration')->set(['current_frontend_theme_id' => $this->id]);
        $sql->prepareStatementForSqlObject($update)->execute();
    }

    /**
     * Gets all theme files
     *
     * @return File[]
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
     * Gets all theme galleries
     *
     * @return Gallery[]
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
     * @return Form[]
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
     * @return Menu[]
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
     * @return SegmentPage[]
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
     * @return SimplePage[]
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
        $this->internalCreate('theme', [
            'scssVariables' => new JsonStrategy(),
            'configuration' => new JsonStrategy(),
        ]);
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
        $this->internalUpdate('theme', [
            'scssVariables' => new JsonStrategy(),
            'configuration' => new JsonStrategy(),
        ]);
    }

    public function format(): array
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