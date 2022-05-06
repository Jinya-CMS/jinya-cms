<?php

namespace App\Tests\Extensions;

use PHPUnit\Runner\BeforeFirstTestHook;

require_once __DIR__ . '/../../../defines.php';

/**
 *
 */
class DefinesHook implements BeforeFirstTestHook
{

    public function executeBeforeFirstTest(): void
    {

    }
}