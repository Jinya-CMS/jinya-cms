<?php

namespace Jinya\Cms\Tests;

use Jinya\Cms\Database\Menu;
use Jinya\Cms\Database\Theme;
use Jinya\Cms\Database\ThemeMenu;
use Jinya\Cms\Theming\ThemeSyncer;
use Faker\Provider\Uuid;
use Jinya\Database\Entity;
use Jinya\Database\Exception\NotNullViolationException;
use Symfony\Component\Filesystem\Filesystem;

abstract class FrontTestCase extends DatabaseAwareTestCase
{
    private static string $name;
    private static Filesystem $fs;
    protected Menu $menu;

    /**
     * @return void
     * @throws NotNullViolationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        Entity::getPDO()->exec('DELETE FROM configuration');
        Entity::getPDO()->exec('INSERT INTO configuration (current_frontend_theme_id) VALUES (null)');
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
        $themeMenu->themeId = $theme->id;
        $themeMenu->menuId = $this->menu->id;
        $themeMenu->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name);
        self::$fs->remove(__ROOT__ . '/public/themes/' . self::$name);
    }
}
