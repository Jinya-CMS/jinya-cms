<?php

namespace App\Database;

use App\Database\Converter\BooleanConverter;
use App\Database\Converter\JsonConverter;
use App\Database\Converter\ThemeDescriptionConverter;
use App\Web\Middleware\AuthorizationMiddleware;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Entity;
use Jinya\Router\Extensions\Database\Attributes\Find;
use stdClass;

/**
 * This class contains all information for themes that are stored in the database.
 * Apart from the database side, there is also filesystem theme information.
 */
#[Table('theme')]
#[Find('/api/theme', new AuthorizationMiddleware(ROLE_READER))]
class Theme extends Entity
{
    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    /** @var array<mixed> The stored configuration in the database
     */
    #[Column]
    #[JsonConverter]
    public array $configuration;

    /** @var string[] The theme description */
    #[Column]
    #[ThemeDescriptionConverter]
    public array $description;

    /** @var string The theme name */
    #[Column]
    public string $name;

    /** @var string The theme display name */
    #[Column(sqlName: 'display_name')]
    public string $displayName;

    /** @var array<string, string> A key-value store of SCSS variables changed by an artist */
    #[Column(sqlName: 'scss_variables')]
    #[JsonConverter]
    public array $scssVariables;

    /** @var bool Specifies whether the theme contains an API theme */
    #[Column(sqlName: 'has_api_theme')]
    #[BooleanConverter]
    public bool $hasApiTheme = false;

    /**
     * Gets the currently active theme
     *
     * @return Theme|null
     * @throws Exception
     */
    public static function getActiveTheme(): ?Theme
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName() . ' AS t')
            ->cols([
                't.id AS id',
                'configuration',
                'description',
                'name',
                'display_name',
                'scss_variables',
                'has_api_theme'
            ])
            ->innerJoin('configuration AS c', 't.id = c.current_frontend_theme_id');

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Finds the theme with the given name
     *
     * @param string $name
     * @return Theme|null
     */
    public static function findByName(string $name): ?Theme
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'configuration',
                'description',
                'name',
                'display_name',
                'scss_variables',
                'has_api_theme'
            ])
            ->where('name = :name', ['name' => $name]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Makes the current theme active
     */
    public function makeActiveTheme(): void
    {
        $query = self::getQueryBuilder()
            ->newDelete()
            ->from('configuration');

        self::executeQuery($query);

        $query = self::getQueryBuilder()
            ->newInsert()
            ->into('configuration')
            ->addRow(['current_frontend_theme_id' => $this->id]);

        self::executeQuery($query);
    }

    /**
     * Gets all theme files
     *
     * @return array<string, File>
     */
    public function getFiles(): array
    {
        $result = [];
        $files = ThemeFile::findByTheme($this->id);
        foreach ($files as $file) {
            $result[$file->name] = $file->getFile();
        }

        return $result;
    }

    /**
     * Gets all theme categories
     *
     * @return array<string, BlogCategory>
     */
    public function getCategories(): array
    {
        $result = [];
        $categories = ThemeBlogCategory::findByTheme($this->id);
        foreach ($categories as $category) {
            $result[$category->name] = $category->getBlogCategory();
        }

        return $result;
    }

    /**
     * Gets all theme galleries
     *
     * @return array<string, Gallery>
     */
    public function getGalleries(): array
    {
        $result = [];
        $galleries = ThemeGallery::findByTheme($this->id);
        foreach ($galleries as $gallery) {
            $result[$gallery->name] = $gallery->getGallery();
        }

        return $result;
    }

    /**
     * Gets all theme forms
     *
     * @return array<string, Form>
     */
    public function getForms(): array
    {
        $result = [];
        $forms = ThemeForm::findByTheme($this->id);
        foreach ($forms as $form) {
            $result[$form->name] = $form->getForm();
        }

        return $result;
    }

    /**
     * Gets all theme menus
     *
     * @return array<string, Menu>
     */
    public function getMenus(): array
    {
        $result = [];
        $menus = ThemeMenu::findByTheme($this->id);
        foreach ($menus as $menu) {
            $result[$menu->name] = $menu->getMenu();
        }

        return $result;
    }

    /**
     * Gets all theme modern pages
     *
     * @return array<string, ModernPage>
     */
    public function getModernPages(): array
    {
        $segmentPages = ThemeModernPage::findByTheme($this->id);
        $result = [];
        foreach ($segmentPages as $segmentPage) {
            $result[$segmentPage->name] = $segmentPage->getModernPage();
        }

        return $result;
    }

    /**
     * Gets all theme classic pages
     *
     * @return array<string, ClassicPage>
     */
    public function getClassicPages(): array
    {
        $pages = ThemeClassicPage::findByTheme($this->id);
        $result = [];
        foreach ($pages as $page) {
            $result[$page->name] = $page->getClassicPage();
        }

        return $result;
    }

    /**
     * Gets all theme assets
     *
     * @return array<string, ThemeAsset>
     */
    public function getAssets(): array
    {
        $assets = ThemeAsset::findByTheme($this->id);
        $result = [];
        foreach ($assets as $asset) {
            $result[$asset->name] = $asset;
        }

        return $result;
    }

    /**
     * Formats the theme into an array
     *
     * @return array<string, array<string, mixed>|int|stdClass|string|bool>
     */
    #[Pure]
    #[ArrayShape([
        'configuration' => 'array|\stdClass',
        'description' => 'array',
        'name' => 'string',
        'displayName' => 'string',
        'scssVariables' => 'array|\stdClass',
        'id' => 'int',
        'hasApi' => 'bool'
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
            'id' => $this->id,
            'hasApi' => $this->hasApiTheme,
        ];
    }
}
