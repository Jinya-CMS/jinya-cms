<?php

namespace App\Tests\Extensions;

use App\Database\Migrations\Migrator;
use PHPUnit\Runner\BeforeFirstTestHook;

class MigrationHook implements BeforeFirstTestHook
{

    /**
     * @throws \App\Database\Exceptions\UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    public function executeBeforeFirstTest(): void
    {
        Migrator::migrate();
    }
}