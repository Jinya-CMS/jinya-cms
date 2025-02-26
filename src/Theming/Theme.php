<?php

namespace Jinya\Cms\Theming;

use Exception;
use Jinya\Cms\Database;
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\ExtensionInterface;
use JShrink\Minifier;
use RuntimeException;
use ScssPhp\ScssPhp\Colors;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\Node\Number;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\Value\SassColor;
use ScssPhp\ScssPhp\ValueConverter;

/**
 * Themes contain all information about the cache state and several helpers for themes
 */
class Theme implements ExtensionInterface
{
    /** @var string Use this in your theme.php if you want to redirect to the homepage if there is any error */
    public const ERROR_BEHAVIOR_HOMEPAGE = 'homepage';
    /** @var string Use this in your theme.php if you want to show an error page if there is any error */
    public const ERROR_BEHAVIOR_ERROR_PAGE = 'errorpage';
    /** @var string The base public path for themes */
    public const BASE_PUBLIC_PATH = '/themes/';
    /** @var string The default cache path for themes */
    public const BASE_CACHE_PATH = __DIR__ . '/../../public' . self::BASE_PUBLIC_PATH;

    /** @var Database\Theme The theme from the database */
    private Database\Theme $dbTheme;
    /** @var Compiler The SCSS compiler used to compile the themes */
    private Compiler $scssCompiler;
    /** @var array<string, mixed> The configuration of the theme */
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
        $this->scssCompiler->registerFunction('jinya-asset', [$this, 'scssJinyaAsset'], ['assetName']);

        $this->parseThemePhp();
    }

    /**
     * Parses the theme.php file
     *
     * @return void
     */
    private function parseThemePhp(): void
    {
        $themePhpFile = ThemeSyncer::THEME_BASE_PATH . $this->dbTheme->name . '/theme.php';
        if (!file_exists($themePhpFile)) {
            throw new RuntimeException("Theme.php is not found, expecting it here: $themePhpFile");
        }

        $this->configuration = require $themePhpFile;
    }

    /**
     * Includes an asset into SCSS
     *
     * @param array<mixed> $args
     * @return array|mixed|Number
     */
    public function scssJinyaAsset(array $args): mixed
    {
        $assets = $this->dbTheme->getAssets();
        $assetName = $this->scssCompiler->getStringText($args[0]);
        if (array_key_exists($assetName, $assets)) {
            return ValueConverter::parseValue('url("' . $assets[$assetName]->publicPath . '")');
        }

        throw new RuntimeException("Asset with name $assetName not found");
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
        $folders = $engine->getFolders();
        if (!array_key_exists('theme', $folders)) {
            $engine->addFolder('theme', ThemeSyncer::THEME_BASE_PATH . $this->dbTheme->name);
        }
        if ($this->dbTheme->hasApiTheme && !array_key_exists('api', $folders)) {
            $engine->addFolder('api', ThemeSyncer::THEME_BASE_PATH . $this->dbTheme->name . '/api');
        }
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
     * @throws SassException
     */
    public function compileStyleCache(): void
    {
        $styleCachePath = self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/';
        if (!@mkdir($styleCachePath, 0777, true) && !is_dir($styleCachePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $styleCachePath));
        }
        $this->clearStyleCache();
        $stylesheets = $this->configuration['styles']['files'] ?? [];
        $this->scssCompiler->addVariables(array_map(static function (string $value) {
            // Allow internal here, if this breaks with an update, we will see it in the unit tests
            $result = Colors::colorNameToColor($value);
            if ($result) {
                return $result;
            }

            if (str_starts_with($value, '#') && in_array(strlen($value), [4, 7])) {
                $hex = substr($value, 1);
                if (strlen($hex) === 3) {
                    $splitHex = mb_str_split($hex);
                    $hex = $splitHex[0] . $splitHex[0] . $splitHex[1] . $splitHex[1] . $splitHex[2] . $splitHex[2];
                }

                if (preg_match('/[[:xdigit:]]{6}/m', $hex)) {
                    $splitInRgb = mb_str_split($hex, 2);
                    $r = (int)hexdec($splitInRgb[0]);
                    $g = (int)hexdec($splitInRgb[1]);
                    $b = (int)hexdec($splitInRgb[2]);

                    return SassColor::rgb($r, $g, $b);
                }
            }

            if (str_starts_with($value, 'rgb(') && str_ends_with($value, ')')) {
                $trimmedRgb = trim(ltrim($value, 'rgb('), ')');
                $splitInRgb = explode(',', str_replace(' ', '', $trimmedRgb));
                $r = (int)$splitInRgb[0];
                $g = (int)$splitInRgb[1];
                $b = (int)$splitInRgb[2];

                if ($r >= 0 && $g >= 0 && $b >= 0 && $r <= 255 && $g <= 255 && $b <= 255) {
                    return SassColor::rgb($r, $g, $b);
                }
            }

            if (str_starts_with($value, 'rgba(') && str_ends_with($value, ')')) {
                $trimmedRgb = trim(ltrim($value, 'rgba('), ')');
                $splitInRgb = explode(',', str_replace(' ', '', $trimmedRgb));
                $r = (int)$splitInRgb[0];
                $g = (int)$splitInRgb[1];
                $b = (int)$splitInRgb[2];

                if ($r >= 0 && $g >= 0 && $b >= 0 && $r <= 255 && $g <= 255 && $b <= 255) {
                    return SassColor::rgb($r, $g, $b, (float)$splitInRgb[3]);
                }
            }

            if (str_starts_with($value, 'hsl(') && str_ends_with($value, ')')) {
                $trimmedHsl = trim(ltrim($value, 'hsl('), ')');
                $splitInHsl = explode(',', str_replace(' ', '', $trimmedHsl));

                return SassColor::hsl(
                    (float)$splitInHsl[0],
                    (float)str_replace('%', '', $splitInHsl[1]),
                    (float)str_replace('%', '', $splitInHsl[2])
                );
            }

            if (str_starts_with($value, 'hsla(') && str_ends_with($value, ')')) {
                $trimmedHsl = trim(ltrim($value, 'hsla('), ')');
                $splitInHsl = explode(',', str_replace(' ', '', $trimmedHsl));

                return SassColor::hsl(
                    (float)$splitInHsl[0],
                    (float)str_replace('%', '', $splitInHsl[1]),
                    (float)str_replace('%', '', $splitInHsl[2]),
                    (float)$splitInHsl[3]
                );
            }

            return ValueConverter::fromPhp($value);
        }, $this->dbTheme->scssVariables));

        foreach ($stylesheets as $stylesheet) {
            if (!file_exists($stylesheet)) {
                continue;
            }

            $this->scssCompiler->setImportPaths(dirname($stylesheet));
            $this->scssCompiler->setOutputStyle(OutputStyle::COMPRESSED);
            $result = $this->scssCompiler->compileString(file_get_contents($stylesheet) ?: '');
            file_put_contents($styleCachePath . uniqid('style', true) . '.css', $result->getCss());
        }
    }

    /**
     * Clears the style cache
     *
     * @return void
     */
    private function clearStyleCache(): void
    {
        foreach ($this->getStyleCache() as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Gets the style cache
     *
     * @return array<string>
     */
    private function getStyleCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles');
        $files = array_map(fn ($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/styles/$item", $files ?: []);

        return array_filter($files, static fn ($item) => is_file($item)) ?: [];
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

    /**
     * Clears the script cache
     *
     * @return void
     */
    private function clearScriptCache(): void
    {
        foreach ($this->getScriptCache() as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Gets the script cache
     *
     * @return array<string>
     */
    private function getScriptCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts');
        $files = array_map(fn ($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/scripts/$item", $files ?: []);

        return array_filter($files, static fn ($item) => is_file($item)) ?: [];
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
            $assetFromDb = Database\ThemeAsset::findByThemeAndName($this->dbTheme->id, $key);

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
                $assetFromDb->themeId = $this->dbTheme->id;
                $assetFromDb->create();
            } else {
                $assetFromDb->publicPath = self::BASE_PUBLIC_PATH . $this->dbTheme->name . '/assets/' . $publicPath;
                $assetFromDb->themeId = $this->dbTheme->id;
                $assetFromDb->update();
            }
        }
    }

    /**
     * Clears the asset cache
     *
     * @return void
     */
    private function clearAssetCache(): void
    {
        foreach ($this->getAssetCache() as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Gets the asset cache
     *
     * @return array<string>
     */
    private function getAssetCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/assets');
        $files = array_map(fn ($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/assets/$item", $files ?: []);

        return array_filter($files, static fn ($item) => is_file($item)) ?: [];
    }

    /**
     * Gets all style variables from the theme configuration
     *
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
     * Gets the configuration structure from the theme.php
     *
     * @return array<string, mixed>
     */
    public function getConfigurationStructure(): array
    {
        return $this->configuration['configurationStructure'] ?? [];
    }

    /**
     * Gets the path to the preview image
     *
     * @return string
     */
    public function getPreviewImagePath(): string
    {
        return $this->configuration['previewImage'] ?? '';
    }
}
