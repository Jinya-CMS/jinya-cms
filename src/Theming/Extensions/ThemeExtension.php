<?php

namespace App\Theming\Extensions;

use App\Database;
use App\Theming;
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\BaseExtension;

/**
 * Provides extensions to the Plates engine, adding helper methods for themes
 */
class ThemeExtension extends BaseExtension
{
    /** @var Theming\Theme The currently active theming theme */
    private Theming\Theme $theme;
    /** @var Database\Theme The currently active database theme */
    private Database\Theme $dbTheme;

    /**
     * Creates a new ThemeExtension class
     *
     * @param Theming\Theme $theme
     * @param Database\Theme $dbTheme
     */
    public function __construct(Theming\Theme $theme, Database\Theme $dbTheme)
    {
        $this->theme = $theme;
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
        $engine->functions->add('asset', [$this, 'asset']);
        $engine->functions->add('config', [$this, 'config']);
        $engine->functions->add('styles', [$this, 'styles']);
        $engine->functions->add('scripts', [$this, 'scripts']);
    }

    /**
     *
     *
     * @param string $name
     * @return Database\ThemeAsset|null
     */
    public function asset(string $name): ?Database\ThemeAsset
    {
        return Database\ThemeAsset::findByThemeAndName($this->dbTheme->id, $name);
    }

    /**
     * Gets a configuration value from the database, if the value is not set in the database, the default value will be set
     *
     * @param string $group The group the configuration value belongs to
     * @param string $field The field the configuration value belongs to
     * @return bool|string|null
     */
    public function config(string $group, string $field): bool|string|null
    {
        return $this->dbTheme->configuration[$group][$field] ?? $this->theme->getConfigurationValues()[$group][$field];
    }

    /**
     * Returns link tags for all configured stylesheets
     *
     * @return string
     */
    public function styles(): string
    {
        if (file_exists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/')) {
            $styleFiles = scandir(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/');
            $styleFiles = array_map(
                fn ($item) => Theming\Theme::BASE_PUBLIC_PATH . $this->dbTheme->name . "/styles/$item",
                $styleFiles ?: []
            );
            $styleFiles = array_filter($styleFiles, static fn ($item) => str_ends_with($item, '.css'));
            $tags = '';
            foreach ($styleFiles as $file) {
                $tags .= "<link type='text/css' rel='stylesheet' href='$file'>";
            }
        } else {
            $tags = '';
        }

        return $tags;
    }

    /**
     * Returns script tags for all configured scripts
     *
     * @return string
     */
    public function scripts(): string
    {
        if (file_exists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/')) {
            $scriptFiles = scandir(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/');
            $scriptFiles = array_map(
                fn ($item) => Theming\Theme::BASE_PUBLIC_PATH . $this->dbTheme->name . "/scripts/$item",
                $scriptFiles ?: []
            );
            $scriptFiles = array_filter($scriptFiles, static fn ($item) => str_ends_with($item, '.js'));
            $tags = '';
            foreach ($scriptFiles as $file) {
                $tags .= "<script src='$file'></script>";
            }
        } else {
            $tags = '';
        }

        return $tags;
    }
}
