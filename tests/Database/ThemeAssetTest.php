<?php

namespace Jinya\Tests\Database;

use App\Database\ThemeAsset;
use App\Tests\ThemeTestCase;

class ThemeAssetTest extends ThemeTestCase
{
    public function testFindByThemeNoAsset(): void
    {
        $assets = ThemeAsset::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($assets));
    }

    public function testFindByTheme(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->id;
        $asset->name = 'Test';
        $asset->create();

        $assets = ThemeAsset::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($assets));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeAsset::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->id;
        $asset->name = 'Test';
        $asset->create();

        $found = ThemeAsset::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($asset, $found);
    }

    public function testDelete(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->id;
        $asset->name = 'Test';
        $asset->create();

        self::assertNotNull(ThemeAsset::findByThemeAndName($this->theme->id, 'Test'));

        $asset->delete();
        self::assertNull(ThemeAsset::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->id;
        $asset->name = 'Test';
        $asset->create();

        $asset->publicPath = '/remote';
        $found = ThemeAsset::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals('/remote', $asset->publicPath);
    }

    public function testCreate(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->id;
        $asset->name = 'Test';
        $asset->create();

        self::assertNotNull(ThemeAsset::findByThemeAndName($this->theme->id, 'Test'));
    }
}
