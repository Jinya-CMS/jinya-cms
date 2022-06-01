<?php

namespace App\Theming\Extensions;

use App\Database;
use App\Theming;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

/**
 *
 */
class ThemeExtension implements ExtensionInterface
{

    private Theming\Theme $theme;
    private Database\Theme $dbTheme;

    /**
     * @param Theming\Theme $theme
     * @param Database\Theme $dbTheme
     */
    public function __construct(Theming\Theme $theme, Database\Theme $dbTheme)
    {
        $this->theme = $theme;
        $this->dbTheme = $dbTheme;
    }

    /**
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
    {
        $engine->registerFunction('asset', [$this, 'asset']);
        $engine->registerFunction('config', [$this, 'config']);
        $engine->registerFunction('styles', [$this, 'styles']);
        $engine->registerFunction('scripts', [$this, 'scripts']);
    }

    /**
     * @param string $name
     * @return Database\ThemeAsset
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function asset(string $name): Database\ThemeAsset
    {
        return $this->dbTheme->getAssets()[$name];
    }

    /**
     * @param string $group
     * @param string $field
     * @return bool|string|null
     */
    public function config(string $group, string $field): bool|string|null
    {
        return $this->dbTheme->configuration[$group][$field] ?? $this->theme->getConfigurationValues()[$group][$field];
    }

    /**
     * @return string
     */
    public function styles(): string
    {
        $styleFiles = scandir(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/');
        $styleFiles = array_map(fn($item) => Theming\Theme::BASE_PUBLIC_PATH . $this->dbTheme->name . "/styles/$item", $styleFiles ?: []);
        $styleFiles = array_filter($styleFiles, static fn($item) => str_ends_with($item, '.css'));
        $tags = '';
        foreach ($styleFiles as $file) {
            $tags .= "<link type='text/css' rel='stylesheet' href='$file'>";
        }

        return $tags;
    }

    /**
     * @return string
     */
    public function scripts(): string
    {
        $scriptFiles = scandir(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/');
        $scriptFiles = array_map(fn($item) => Theming\Theme::BASE_PUBLIC_PATH . $this->dbTheme->name . "/scripts/$item", $scriptFiles ?: []);
        $scriptFiles = array_filter($scriptFiles, static fn($item) => str_ends_with($item, '.js'));
        $tags = '';
        foreach ($scriptFiles as $file) {
            $tags .= "<script src='$file'></script>";
        }

        return $tags;
    }
}
