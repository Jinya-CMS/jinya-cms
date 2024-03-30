<?php

namespace Jinya\Cms\Theming;

use Jinya\Cms\Database\Theme;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use Symfony\Component\Filesystem\Filesystem;

class ThemeSyncerTest extends DatabaseAwareTestCase
{
    private static string $name;
    private static Filesystem $fs;

    public function testSyncThemes(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName(self::$name);
        self::assertNotNull($theme);
    }

    public function testSyncThemesUpdate(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName(self::$name);
        self::assertNotNull($theme);

        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName(self::$name);
        self::assertNotNull($theme);
    }

    public function testSyncThemesDelete(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName(self::$name);
        self::assertNotNull($theme);
        $theme->makeActiveTheme();

        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name);
        self::$fs->remove(__ROOT__ . '/public/themes/' . self::$name);

        $themeSyncer->syncThemes();

        $theme = Theme::findByName(self::$name);
        self::assertNull($theme);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$name = Uuid::uuid();
        self::$fs = new Filesystem();

        self::$fs->mirror(__ROOT__ . '/tests/files/theme/unit-test-theme', ThemeSyncer::THEME_BASE_PATH . self::$name);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name);
        self::$fs->remove(__ROOT__ . '/public/themes/' . self::$name);
    }
}
