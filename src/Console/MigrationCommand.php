<?php

namespace App\Console;

use App\Database\Migrations\Migrator;

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
        Migrator::migrate();
        $this->climate->info("Migrations were successfully executed");
    }
}
