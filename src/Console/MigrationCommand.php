<?php

namespace App\Console;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Migrations\Migrator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use JsonException;

#[JinyaCommand("migrate")]
class MigrationCommand extends AbstractCommand
{
    /**
     * @throws JsonException
     */
    public function run(): void
    {
        $this->climate->info('Starting database migration');
        try {
            $executedMigrations = Migrator::migrate();
            if ($executedMigrations > 0) {
                $this->climate->info("$executedMigrations migration(s) were successfully executed");
            } else {
                $this->climate->yellow('There were no new migrations found');
            }
        } catch (ForeignKeyFailedException $e) {
            $this->climate->to('error')->error('Failed to run migration, a foreign key exception was thrown');
            $this->climate->to('error')->error($e->getMessage());
            $this->climate->to('error')->error($e->getTraceAsString());
        } catch (InvalidQueryException $e) {
            $this->climate->error('Failed to run migration, a invalid query exception was thrown');
            $errorData = json_encode($e->errorInfo, flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            $this->climate->to('error')->error('The error data are:');
            $this->climate->to('error')->error($errorData);
            $this->climate->to('error')->error($e->getMessage());
            $this->climate->to('error')->error($e->getTraceAsString());
        } catch (UniqueFailedException $e) {
            $this->climate->to('error')->error('Failed to run migration, a unique failed exception was thrown');
            $this->climate->to('error')->error($e->getMessage());
            $this->climate->to('error')->error($e->getTraceAsString());
        }
    }
}
