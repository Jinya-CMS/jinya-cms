<?php

namespace App\Console;

use App\Theming\ThemeSyncer;

#[JinyaCommand('theme-sync')]
class ThemeSyncCommand extends AbstractCommand
{

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $this->climate->info('Syncing all themes');
        (new ThemeSyncer())->syncThemes();
        $this->climate->info('Synced all themes');
    }
}