<?php

namespace Jinya\Components\Database;

use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseMigrator implements DatabaseMigratorInterface
{
    /**
     * Migrate the database to the latest version
     * @throws Exception
     */
    public function migrate(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            // (optional) define the value of command arguments
            '--no-interaction' => true,
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * Get the latest migration
     *
     * @return string
     * @throws Exception
     */
    public function getLatestMigrationVersion(): string
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:latest',
            '--no-interaction' => true,
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        return trim($content);
    }

    /**
     * Sets the given version as the current migration version
     *
     * @param string $version
     * @throws Exception
     */
    public function setMigrationVersion(string $version): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:version',
            // (optional) define the value of command arguments
            '--no-interaction' => true,
            '--add' => true,
            'version' => $version,
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * Activates all migrations
     * @throws Exception
     */
    public function activateAllMigrations(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:version',
            // (optional) define the value of command arguments
            '--no-interaction' => true,
            '--all' => true,
            '--add' => true,
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);
    }
}
