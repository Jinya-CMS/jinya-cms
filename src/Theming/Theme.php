<?php

namespace App\Theming;

use App\Database;
use Exception;
use JShrink\Minifier;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use RuntimeException;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Crunched;

class Theme implements ExtensionInterface
{
    private const BASE_CACHE_PATH = __DIR__ . '/../../public/themes/';
    private Database\Theme $dbTheme;
    private Compiler $scssCompiler;
    private array $configuration;

    /**
     * Theme constructor.
     * @param Database\Theme $dbTheme
     */
    public function __construct(Database\Theme $dbTheme)
    {
        $this->dbTheme = $dbTheme;
        $this->scssCompiler = new Compiler();
        $this->scssCompiler->setSourceMap(Compiler::SOURCE_MAP_NONE);
        $this->scssCompiler->setFormatter(Crunched::class);
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
     * @return Theme
     */
    public static function getActiveTheme(): Theme
    {
        return new self(Database\Theme::getActiveTheme());
    }

    /**
     * Registers the theme in the engine
     *
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->addFolder('theme', ThemeSyncer::THEME_BASE_PATH . $this->dbTheme->name);
        $engine->addData([
            'theme' => $this->dbTheme,
            'configuration' => array_merge($this->getConfigurationValues(), $this->dbTheme->configuration),
        ]);
        $engine->registerFunction('getStyleTags', function () {
            $styleFiles = $this->getStyleCache();
            $tags = '';
            foreach ($styleFiles as $file) {
                $tags .= "<link type='text/css' rel='stylesheet' href='$file'>";
            }

            return $tags;
        });
        $engine->registerFunction('getScriptTags', function () {
            $styleFiles = $this->getStyleCache();
            $tags = '';
            foreach ($styleFiles as $file) {
                $tags .= "<script type='javascript' src='$file'></script>";
            }

            return $tags;
        });
    }

    /**
     * Gets the configuration values
     *
     * @return array
     */
    public function getConfigurationValues(): array
    {
        return $this->configuration['configuration'] ?? [];
    }

    private function getStyleCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles');
        $files = array_map(fn($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/styles/$item", $files);

        return array_filter($files, fn($item) => is_file($item));
    }

    /**
     * Compiles the style cache of the given theme
     */
    public function compileStyleCache(): void
    {
        $this->clearStyleCache();
        $stylesheets = $this->configuration['styles'] ?? [];
        $styleCachePath = self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/';
        $this->scssCompiler->setVariables($this->dbTheme->scssVariables);

        foreach ($stylesheets as $stylesheet) {
            if (!file_exists($stylesheet)) {
                continue;
            }

            $this->scssCompiler->setImportPaths(dirname($stylesheet));
            $result = $this->scssCompiler->compile(file_get_contents($stylesheet));
            file_put_contents($styleCachePath . uniqid('style', true), $result);
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
     * Compiles the script cache of the given theme
     *
     * @throws Exception
     */
    public function compileScriptCache(): void
    {
        $this->clearScriptCache();
        $scripts = $this->configuration['scripts'] ?? [];
        $scriptCachePath = self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/';

        foreach ($scripts as $script) {
            if (!file_exists($script)) {
                continue;
            }

            $result = Minifier::minify(file_get_contents($script));
            file_put_contents($scriptCachePath . uniqid('script', true), $result);
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

    private function getScriptCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts');
        $files = array_map(fn($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/scripts/$item", $files);

        return array_filter($files, fn($item) => is_file($item));
    }

    public function getStyleVariables(): array
    {
        $variablesPath = $this->configuration['styleVariablesPath'];
        if (!is_file($variablesPath)) {
            return [];
        }

        $handle = fopen($variablesPath, 'rb+');
        $variables = [];

        try {
            if ($handle) {
                while (false !== ($line = fgets($handle))) {
                    if (preg_match('/^\$.*!default;\s$/', $line)) {
                        $replaced = preg_replace('/ !default;$/', '', $line);
                        $exploded = explode(':', $replaced);
                        $variables[$exploded[0]] = preg_replace('/^\s|\s$/', '', $exploded[1]);
                    }
                }
            }
        } finally {
            fclose($handle);
        }
        return $variables;
    }

    public function getConfigurationStructure(): array
    {
        return $this->configuration['configurationStructure'] ?? [];
    }

    public function getPreviewImagePath(): string
    {
        return $this->configuration['previewImage'] ?? '';
    }
}