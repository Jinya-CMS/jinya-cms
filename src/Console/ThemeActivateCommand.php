<?php

namespace App\Console;

use App\Database\Theme;
use App\Theming\ThemeSyncer;
use Exception;
use Jinya\Database\Exception\NotNullViolationException;

/** @codeCoverageIgnore */
#[JinyaCommand('theme-activate')]
class ThemeActivateCommand extends AbstractCommand
{
    /**
     * @return void
     * @throws NotNullViolationException
     * @throws Exception
     */
    public function run(): void
    {
        $this->climate->arguments->add([
            'theme' => [
                'longPrefix' => 'theme',
                'required' => true
            ],
        ]);
        (new ThemeSyncer())->syncThemes();
        $this->climate->arguments->parse();
        $dbTheme = Theme::findByName((string)$this->climate->arguments->get('theme'));
        if (!$dbTheme) {
            $this->climate->error('Theme not found');
            return;
        }
        $this->climate->info("Compiling theme $dbTheme->displayName");
        $themingTheme = new \App\Theming\Theme($dbTheme);
        $this->climate->info('Compiling asset cache');
        $themingTheme->compileAssetCache();
        $this->climate->info('Compiling script cache');
        $themingTheme->compileScriptCache();
        $this->climate->info('Compiling style cache');
        $themingTheme->compileStyleCache();
        $this->climate->info("Compiled theme $dbTheme->displayName");

        $dbTheme->makeActiveTheme();
    }
}
