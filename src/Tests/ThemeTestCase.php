<?php

namespace App\Tests;

use App\Database\Theme;
use Faker\Provider\Uuid;
use Jinya\Database\Exception\NotNullViolationException;

abstract class ThemeTestCase extends DatabaseAwareTestCase
{
    protected Theme $theme;

    /**
     * @return void
     * @throws NotNullViolationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $theme = new Theme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->hasApiTheme = true;
        $theme->create();

        $this->theme = $theme;
    }
}
