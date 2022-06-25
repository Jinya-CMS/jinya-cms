<?php

namespace App\Tests\Extensions;

use Faker\Provider\UserAgent;
use PHPUnit\Runner\BeforeFirstTestHook;

class PrepareIniValuesHook implements BeforeFirstTestHook
{

    public function executeBeforeFirstTest(): void
    {
        ini_set('user_agent', UserAgent::firefox());
    }
}