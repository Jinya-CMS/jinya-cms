<?php

namespace App\Theming;

use App\Database;
use League\Plates\Engine;

class Theme
{
    private const BASE_PATH = __DIR__ . '/../../themes/';
    private const BASE_CACHE_PATH = __DIR__ . '/../../cache/themes/';
    private Database\Theme $dbTheme;

    /**
     * Theme constructor.
     * @param Database\Theme $dbTheme
     */
    public function __construct(Database\Theme $dbTheme)
    {
        $this->dbTheme = $dbTheme;
    }

    public function includeIntoEngine(Engine $engine): Engine
    {
        $engine->addFolder('theme', self::BASE_PATH . $this->dbTheme->name);

        return $engine;
    }

    public function compileStyleCache(): void
    {

    }

    public function compileScriptCache(): void
    {

    }

    public function getStyleVariables(): array
    {
        return [];
    }

    public function getConfigurationValues(): array
    {
        return [];
    }

    public function getConfigurationStructure(): array
    {
        return [];
    }

    public function getPreviewImagePath(): string
    {
        return '';
    }
}