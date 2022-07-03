<?php

namespace App\Console;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Migrations\Migrator;
use Jinya\PDOx\Exceptions\InvalidQueryException;

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
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     */
    public function run(): void
    {
        $this->climate->info('Starting database migration');
        $executedMigrations = Migrator::migrate();
        if ($executedMigrations > 0) {
            $this->climate->info("$executedMigrations migration(s) were successfully executed");
        } else {
            $this->climate->yellow('There were no new migrations found');
        }
    }
}
