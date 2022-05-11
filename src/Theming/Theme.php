<?php

namespace App\Theming;

use App\Database;
use Exception;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use JShrink\Minifier;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use RuntimeException;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

/**
 *
 */
class Theme implements ExtensionInterface
{
    public const ERROR_BEHAVIOR_HOMEPAGE = 'homepage';
    public const ERROR_BEHAVIOR_ERROR_PAGE = 'errorpage';

    private const BASE_PUBLIC_PATH = '/themes/';
    private const BASE_CACHE_PATH = __DIR__ . '/../../public' . self::BASE_PUBLIC_PATH;
    private Database\Theme $dbTheme;
    private Compiler $scssCompiler;
    /** @var array<string, mixed> */
    private array $configuration;

    /**
     * Theme constructor.
     */
    public function __construct(Database\Theme $dbTheme)
    {
        $this->dbTheme = $dbTheme;
        $this->scssCompiler = new Compiler();
        $this->scssCompiler->setSourceMap(Compiler::SOURCE_MAP_NONE);
        $this->scssCompiler->setOutputStyle(OutputStyle::COMPRESSED);
        $this->scssCompiler->registerFunction('jinya-asset', function (array $args) {
            $assets = $this->dbTheme->getAssets();
            $assetName = $this->scssCompiler->getStringText($args[0]);
            if (array_key_exists($assetName, $assets)) {
                return 'url("' . $assets[$assetName]->publicPath . '")';
            }

            throw new RuntimeException("Asset with name $assetName not found");
        }, ['assetName']);

        $this->parseThemePhp();
    }

    private function parseThemePhp(): void
    {
        $themePhpFile = ThemeSyncer::THEME_BASE_PATH . $this->dbTheme->name . '/theme.php';
        if (!file_exists($themePhpFile)) {
            throw new RuntimeException("Theme.php is not found, expecting it here: $themePhpFile");
        }

        $this->configuration = require $themePhpFile;
    }

    /**
     * Gets the currently activated theme
     *
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     */
    public static function getActiveTheme(): Theme
    {
        return new self(Database\Theme::getActiveTheme());
    }

    /**
     * Gets the desired error behavior
     */
    public function getErrorBehavior(): string
    {
        return $this->configuration['errorBehavior'] ?? self::ERROR_BEHAVIOR_HOMEPAGE;
    }

    /**
     * Registers the theme in the engine
     *
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
    {
        $engine->addFolder('theme', ThemeSyncer::THEME_BASE_PATH . $this->dbTheme->name);

        // $this->segmentPage(name)
        $engine->registerFunction('segmentPage', fn(string $name) => $this->dbTheme->getSegmentPages()[$name]);

        // $this->hasSegmentPage(name)
        $engine->registerFunction('hasSegmentPage', fn(string $name) => isset($this->dbTheme->getSegmentPages()[$name]));

        // $this->page(name)
        $engine->registerFunction('page', fn(string $name) => $this->dbTheme->getPages()[$name]);

        // $this->hasPage(name)
        $engine->registerFunction('hasPage', fn(string $name) => isset($this->dbTheme->getPages()[$name]));

        // $this->simplePage(name)
        $engine->registerFunction('simplePage', fn(string $name) => $this->dbTheme->getPages()[$name]);

        // $this->hasSimplePage(name)
        $engine->registerFunction('hasSimplePage', fn(string $name) => isset($this->dbTheme->getPages()[$name]));

        // $this->file(name)
        $engine->registerFunction('file', fn(string $name) => $this->dbTheme->getFiles()[$name]);

        // $this->hasFile(name)
        $engine->registerFunction('hasFile', fn(string $name) => isset($this->dbTheme->getFiles()[$name]));

        // $this->gallery(name)
        $engine->registerFunction('gallery', fn(string $name) => $this->dbTheme->getGalleries()[$name]);

        // $this->hasGallery(name)
        $engine->registerFunction('hasGallery', fn(string $name) => isset($this->dbTheme->getGalleries()[$name]));

        // $this->menu(name)
        $engine->registerFunction('menu', fn(string $name) => $this->dbTheme->getMenus()[$name]);

        // $this->hasMenu(name)
        $engine->registerFunction('hasMenu', fn(string $name) => isset($this->dbTheme->getMenus()[$name]));

        // $this->asset(name)
        $engine->registerFunction('asset', fn(string $name) => $this->dbTheme->getAssets()[$name]);

        // $this->form(name)
        $engine->registerFunction('form', fn(string $name) => $this->dbTheme->getForms()[$name]);

        // $this->hasForm(name)
        $engine->registerFunction('hasForm', fn(string $name) => isset($this->dbTheme->getForms()[$name]));

        // $this->blogCategory(name)
        $engine->registerFunction('blogCategory', fn(string $name) => $this->dbTheme->getCategories()[$name]);

        // $this->hasBlogCategory(name)
        $engine->registerFunction('hasBlogCategory', fn(string $name) => isset($this->dbTheme->getCategories()[$name]));

        // $this->config(group, field)
        $engine->registerFunction(
            'config',
            fn(
                string $group,
                string $field
            ) => $this->dbTheme->configuration[$group][$field] ?? $this->configuration['configuration'][$group][$field]
        );

        // $this->styles()
        $engine->registerFunction(
            'styles',
            function () {
                $styleFiles = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/');
                $styleFiles = array_map(
                    fn($item) => self::BASE_PUBLIC_PATH . $this->dbTheme->name . "/styles/$item", $styleFiles ?: [],
                );
                $styleFiles = array_filter($styleFiles, static fn($item) => str_ends_with($item, '.css'));
                $tags = '';
                foreach ($styleFiles as $file) {
                    $tags .= "<link type='text/css' rel='stylesheet' href='$file'>";
                }

                return $tags;
            },
        );

        // $this->scripts()
        $engine->registerFunction(
            'scripts',
            function () {
                $scriptFiles = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/');
                $scriptFiles = array_map(
                    fn($item) => self::BASE_PUBLIC_PATH . $this->dbTheme->name . "/scripts/$item", $scriptFiles ?: [],
                );
                $scriptFiles = array_filter($scriptFiles, static fn($item) => str_ends_with($item, '.js'));
                $tags = '';
                foreach ($scriptFiles as $file) {
                    $tags .= "<script src='$file'></script>";
                }

                return $tags;
            },
        );
    }

    /**
     * Gets the configuration values
     *
     * @return array<string, mixed>
     */
    public function getConfigurationValues(): array
    {
        return $this->configuration['configuration'] ?? [];
    }

    /**
     * Compiles the style cache of the given theme
     *
     * @return void
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     */
    public function compileStyleCache(): void
    {
        $styleCachePath = self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/';
        if (!@mkdir($styleCachePath, 0777, true) && !is_dir($styleCachePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $styleCachePath));
        }
        $this->clearStyleCache();
        $stylesheets = $this->configuration['styles']['files'] ?? [];
        $this->scssCompiler->addVariables($this->dbTheme->scssVariables);

        foreach ($stylesheets as $stylesheet) {
            if (!file_exists($stylesheet)) {
                continue;
            }

            $this->scssCompiler->setImportPaths(dirname($stylesheet));
            $result = $this->scssCompiler->compileString(file_get_contents($stylesheet) ?: '');
            file_put_contents($styleCachePath . uniqid('style', true) . '.css', $result->getCss());
        }
    }

    private function clearStyleCache(): void
    {
        foreach ($this->getStyleCache() as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @return array<string>
     */
    private function getStyleCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles');
        $files = array_map(fn($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/styles/$item", $files ?: []);

        return array_filter($files, static fn($item) => is_file($item)) ?: [];
    }

    /**
     * Compiles the script cache of the given theme
     *
     * @throws Exception
     */
    public function compileScriptCache(): void
    {
        $scriptCachePath = self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/';
        if (!@mkdir($scriptCachePath, 0777, true) && !is_dir($scriptCachePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $scriptCachePath));
        }
        $this->clearScriptCache();
        $scripts = $this->configuration['scripts'] ?? [];

        foreach ($scripts as $script) {
            if (!file_exists($script)) {
                continue;
            }

            $result = Minifier::minify(file_get_contents($script) ?: '');
            file_put_contents($scriptCachePath . uniqid('script', true) . '.js', $result);
        }
    }

    private function clearScriptCache(): void
    {
        foreach ($this->getScriptCache() as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @return array<string>
     */
    private function getScriptCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts');
        $files = array_map(fn($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/scripts/$item", $files ?: []);

        return array_filter($files, static fn($item) => is_file($item)) ?: [];
    }

    /**
     * Compiles the asset cache of the given theme
     *
     * @throws Exception
     */
    public function compileAssetCache(): void
    {
        $assetCachePath = self::BASE_CACHE_PATH . $this->dbTheme->name . '/assets/';
        if (!@mkdir($assetCachePath, 0777, true) && !is_dir($assetCachePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $assetCachePath));
        }
        $this->clearAssetCache();
        $assets = $this->configuration['assets'] ?? [];

        foreach ($assets as $key => $asset) {
            $assetFromDb = Database\ThemeAsset::findByThemeAndName($this->dbTheme->getIdAsInt(), $key);

            if (!file_exists($asset)) {
                $assetFromDb?->delete();

                continue;
            }

            $publicPath = uniqid('asset', true) . '.' . pathinfo($asset, PATHINFO_EXTENSION);
            copy($asset, $assetCachePath . $publicPath);

            if ($assetFromDb === null) {
                $assetFromDb = new Database\ThemeAsset();
                $assetFromDb->name = $key;
                $assetFromDb->publicPath = self::BASE_PUBLIC_PATH . $this->dbTheme->name . '/assets/' . $publicPath;
                $assetFromDb->themeId = $this->dbTheme->getIdAsInt();
                $assetFromDb->create();
            } else {
                $assetFromDb->publicPath = self::BASE_PUBLIC_PATH . $this->dbTheme->name . '/assets/' . $publicPath;
                $assetFromDb->themeId = $this->dbTheme->getIdAsInt();
                $assetFromDb->update();
            }
        }
    }

    private function clearAssetCache(): void
    {
        foreach ($this->getAssetCache() as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @return array<string>
     */
    private function getAssetCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/assets');
        $files = array_map(fn($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/assets/$item", $files ?: []);

        return array_filter($files, static fn($item) => is_file($item)) ?: [];
    }

    /**
     * @return array<string>
     */
    public function getStyleVariables(): array
    {
        $variablesPath = $this->configuration['styles']['variables'];
        if (!is_file($variablesPath)) {
            return [];
        }

        $handle = fopen($variablesPath, 'rb+');
        $variables = [];

        try {
            if (is_resource($handle)) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/^\$.*!default;\s$/', $line)) {
                        $replaced = preg_replace('/ !default;$/', '', $line);
                        $exploded = explode(':', $replaced);
                        $variables[$exploded[0]] = preg_replace('/^\s|\s$/', '', $exploded[1]);
                    }
                }
            }
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        return $variables;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfigurationStructure(): array
    {
        return $this->configuration['configurationStructure'] ?? [];
    }

    public function getPreviewImagePath(): string
    {
        return $this->configuration['previewImage'] ?? '';
    }
}
