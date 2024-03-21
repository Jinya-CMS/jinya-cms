<?php

namespace App\Console;

use App\Database\Theme;

/** @codeCoverageIgnore */
#[JinyaCommand('theme-compile')]
class ThemeCompileCommand extends AbstractCommand
{
    public function run(): void
    {
        $this->climate->arguments->add([
            'all' => [
                'noValue' => true,
                'longPrefix' => 'all'
            ],
            'theme' => [
                'defaultValue' => '',
                'longPrefix' => 'theme'
            ],
        ]);
        $this->climate->arguments->parse();
        if ($this->climate->arguments->get('all')) {
            $allThemes = iterator_to_array(Theme::findAll());
            $this->climate->info('Compiling all themes');
            $progress = $this->climate->progress(count($allThemes));

            foreach ($allThemes as $dbTheme) {
                $progress->advance(1, $dbTheme->displayName);
                $themingTheme = new \App\Theming\Theme($dbTheme);
                $themingTheme->compileAssetCache();
                $themingTheme->compileScriptCache();
                $themingTheme->compileStyleCache();
            }

            $this->climate->info('Compiled themes');
        } elseif ($this->climate->arguments->get('theme')) {
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
        } else {
            $allThemes = iterator_to_array(Theme::findAll());
            $options = [];
            foreach ($allThemes as $dbTheme) {
                $options[$dbTheme->name] = $dbTheme->displayName;
            }
            $checkboxes = $this->climate->checkboxes('Choose themes to compile', $options);
            $response = $checkboxes->prompt();
            $progress = $this->climate->progress(count($response));
            foreach ($response as $name) {
                $dbTheme = Theme::findByName($name);
                $progress->advance(1, $dbTheme->displayName);
                $themingTheme = new \App\Theming\Theme($dbTheme);
                $themingTheme->compileAssetCache();
                $themingTheme->compileScriptCache();
                $themingTheme->compileStyleCache();
            }
        }
    }
}
