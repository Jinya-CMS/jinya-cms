<?php

namespace App\Console;

use App\Theming\ThemeSyncer;
use Jinya\Database\Exception\NotNullViolationException;

#[JinyaCommand('theme-sync')]
class ThemeSyncCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     * @throws NotNullViolationException
     */
    public function run(): void
    {
        $this->climate->info('Syncing all themes');
        (new ThemeSyncer())->syncThemes();
        $this->climate->info('Synced all themes');
    }
}
