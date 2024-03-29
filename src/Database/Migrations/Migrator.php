<?php

namespace App\Database\Migrations;

use Jinya\Database\Migration\Migrator as DatabaseMigrator;
use Migrations\ApiThemeOption;
use Migrations\ArtistDarkLightSwitch;
use Migrations\Blog;
use Migrations\BruteForcePrevention;
use Migrations\CategoryNameNotUnique;
use Migrations\CategoryWebhook;
use Migrations\CollationUtf8Mb4;
use Migrations\FileTags;
use Migrations\FormItemBoolColumns;

/**
 * Abstract static class to migrate the Jinya database to the most recent state
 */
abstract class Migrator
{
    /**
     * Migrates the installation of Jinya
     *
     * @return int
     */
    public static function migrate(): int
    {
        $migrations = [
            new InitialMigration(),
            new BruteForcePrevention(),
            new Blog(),
            new CategoryWebhook(),
            new CategoryNameNotUnique(),
            new CollationUtf8Mb4(),
            new FormItemBoolColumns(),
            new ArtistDarkLightSwitch(),
            new ApiThemeOption(),
            new FileTags(),
        ];

        DatabaseMigrator::migrateUp($migrations, 'migration_state');

        return 1;
    }
}
