<?php

namespace App\Tests;

use App\Database\Theme;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;

class ThemeTestCase extends TestCase
{
    protected Theme $theme;

    protected function setUp(): void
    {
        $theme = new Theme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->create();

        $this->theme = $theme;
    }
}