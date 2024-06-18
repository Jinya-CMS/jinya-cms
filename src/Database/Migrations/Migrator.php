<?php

namespace Jinya\Cms\Database\Migrations;

use Jinya\Cms\Migrations\BlogPostUpdated;
use Jinya\Cms\Migrations\TG191Analytics;
use Jinya\Cms\Migrations\TG192JinyaConfiguration;
use Jinya\Cms\Migrations\TG195AppTotp;
use Jinya\Cms\Migrations\TG201EmailPreferences;
use Jinya\Cms\Migrations\TG217IpDatabase;
use Jinya\Database\Migration\Migrator as DatabaseMigrator;
use Jinya\Cms\Migrations\ApiThemeOption;
use Jinya\Cms\Migrations\ArtistDarkLightSwitch;
use Jinya\Cms\Migrations\Blog;
use Jinya\Cms\Migrations\BruteForcePrevention;
use Jinya\Cms\Migrations\CategoryNameNotUnique;
use Jinya\Cms\Migrations\CategoryWebhook;
use Jinya\Cms\Migrations\CollationUtf8Mb4;
use Jinya\Cms\Migrations\FileTags;
use Jinya\Cms\Migrations\FormItemBoolColumns;

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
    public static function migrate(bool $inCli = false): int
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
            new BlogPostUpdated(),
            new TG195AppTotp(),
            new TG201EmailPreferences(),
            new TG192JinyaConfiguration(),
            new TG217IpDatabase($inCli),
            new TG191Analytics()
        ];

        DatabaseMigrator::migrateUp($migrations, 'migration_state');

        return 1;
    }
}
