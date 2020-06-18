<?php

namespace App\Theming;

use App\Database;
use Exception;
use JShrink\Minifier;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Crunched;

class Theme implements ExtensionInterface
{
    private const BASE_PUBLIC_PATH = '/themes/';
    private const BASE_CACHE_PATH = __DIR__ . '/../../public' . self::BASE_PUBLIC_PATH;
    private Database\Theme $dbTheme;
    private Compiler $scssCompiler;
    private array $configuration;

    /**
     * Theme constructor.
     * @param Database\Theme $dbTheme
     * @param LoggerInterface $logger
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
            $styleFiles = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/');
            $styleFiles = array_map(fn($item) => self::BASE_PUBLIC_PATH . $this->dbTheme->name . "/styles/$item",
                $styleFiles);
            $styleFiles = array_filter($styleFiles, fn($item) => substr($item, -strlen('.css')) === '.css');
            $tags = '';
            foreach ($styleFiles as $file) {
                $tags .= "<link type='text/css' rel='stylesheet' href='$file'>";
            }

            return $tags;
        });
        $engine->registerFunction('getScriptTags', function () {
            $scriptFiles = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/');
            $scriptFiles = array_map(fn($item) => self::BASE_PUBLIC_PATH . $this->dbTheme->name . "/scripts/$item",
                $scriptFiles);
            $scriptFiles = array_filter($scriptFiles, fn($item) => substr($item, -strlen('.js')) === '.js');
            $tags = '';
            foreach ($scriptFiles as $file) {
                $tags .= "<script src='$file'></script>";
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

    /**
     * Compiles the style cache of the given theme
     */
    public function compileStyleCache(): void
    {
        $this->clearStyleCache();
        $stylesheets = $this->configuration['styles']['files'] ?? [];
        $styleCachePath = self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/';
        if (!mkdir($styleCachePath, 0777, true) && !is_dir($styleCachePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $styleCachePath));
        }
        $this->scssCompiler->setVariables($this->dbTheme->scssVariables);

        foreach ($stylesheets as $stylesheet) {
            if (!file_exists($stylesheet)) {
                continue;
            }

            $this->scssCompiler->setImportPaths(dirname($stylesheet));
            $result = $this->scssCompiler->compile(file_get_contents($stylesheet));
            file_put_contents($styleCachePath . uniqid('style', true) . '.css', $result);
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

    private function getStyleCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/styles');
        $files = array_map(fn($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/styles/$item", $files);

        return array_filter($files, fn($item) => is_file($item)) ?? [];
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
        if (!mkdir($scriptCachePath, 0777, true) && !is_dir($scriptCachePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $scriptCachePath));
        }

        foreach ($scripts as $script) {
            if (!file_exists($script)) {
                continue;
            }

            $result = Minifier::minify(file_get_contents($script));
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

    private function getScriptCache(): array
    {
        $files = scandir(self::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts');
        $files = array_map(fn($item) => self::BASE_CACHE_PATH . $this->dbTheme->name . "/scripts/$item", $files);

        return array_filter($files, fn($item) => is_file($item)) ?? [];
    }

    public function getStyleVariables(): array
    {
        $variablesPath = $this->configuration['styles']['variables'];
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