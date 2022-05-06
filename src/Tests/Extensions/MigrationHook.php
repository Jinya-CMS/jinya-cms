<?php

namespace App\Tests\Extensions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Migrations\Migrator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use PHPUnit\Runner\BeforeFirstTestHook;

/**
 *
 */
class MigrationHook implements BeforeFirstTestHook
{

    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function executeBeforeFirstTest(): void
    {
        Migrator::migrate();
    }
}