<?php

namespace App\Tests;

use App\Database\Menu;
use App\Database\Theme;
use App\Database\ThemeMenu;
use App\Database\Utils\LoadableEntity;
use App\Theming\ThemeSyncer;
use Faker\Provider\Uuid;
use Symfony\Component\Filesystem\Filesystem;

abstract class FrontTestCase extends DatabaseAwareTestCase
{
    private static string $name;
    private static Filesystem $fs;
    protected Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();
        LoadableEntity::executeSqlString('DELETE FROM configuration');
        LoadableEntity::executeSqlString('INSERT INTO configuration (current_frontend_theme_id) VALUES (null)');
        self::$name = Uuid::uuid();
        self::$fs = new Filesystem();

        self::$fs->mirror(__ROOT__ . '/tests/files/theme/unit-test-theme', ThemeSyncer::THEME_BASE_PATH . self::$name);
        $syncer = new ThemeSyncer();
        $syncer->syncThemes();
        $theme = Theme::findByName(self::$name);
        $theme->makeActiveTheme();

        $this->menu = new Menu();
        $this->menu->name = Uuid::uuid();
        $this->menu->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->name = 'menu1';
        $themeMenu->themeId = $theme->getIdAsInt();
        $themeMenu->menuId = $this->menu->getIdAsInt();
        $themeMenu->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name);
        self::$fs->remove(__ROOT__ . '/public/themes/' . self::$name);
    }
}