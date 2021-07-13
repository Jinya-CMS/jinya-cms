<?php

namespace App\Tests\Extensions;

use App\Database\Migrations\Migrator;
use PHPUnit\Runner\BeforeFirstTestHook;

class MigrationHook implements BeforeFirstTestHook
{

    public function executeBeforeFirstTest(): void
    {
        Migrator::migrate();
    }
}