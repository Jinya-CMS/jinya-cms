<?php

namespace Jinya\Cms\Console;

use Jinya\Cms\Database\Migrations\Migrator;

/**
 * This command migrates the Jinya CMS database to the most recent version
 */
#[JinyaCommand('migrate')]
class MigrationCommand extends AbstractCommand
{
    /**
     * Executes the migration command
     *
     * @return void
     */
    public function run(): void
    {
        $this->climate->info('Starting database migration');
        Migrator::migrate(true);
        $this->climate->info("Migrations were successfully executed");
    }
}
