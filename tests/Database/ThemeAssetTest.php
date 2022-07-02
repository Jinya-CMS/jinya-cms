<?php

namespace Jinya\Tests\Database;

use App\Database\ThemeAsset;
use App\Tests\ThemeTestCase;

class ThemeAssetTest extends ThemeTestCase
{
    public function testFindByThemeNoAsset(): void
    {
        $assets = ThemeAsset::findByTheme($this->theme->getIdAsInt());
        self::assertCount(0, $assets);
    }

    public function testFindByTheme(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->getIdAsInt();
        $asset->name = 'Test';
        $asset->create();

        $assets = ThemeAsset::findByTheme($this->theme->getIdAsInt());
        self::assertCount(1, $assets);
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeAsset::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->getIdAsInt();
        $asset->name = 'Test';
        $asset->create();

        $found = ThemeAsset::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertEquals($asset->format(), $found->format());
    }

    public function testDelete(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->getIdAsInt();
        $asset->name = 'Test';
        $asset->create();

        self::assertNotNull(ThemeAsset::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));

        $asset->delete();
        self::assertNull(ThemeAsset::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testUpdate(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->getIdAsInt();
        $asset->name = 'Test';
        $asset->create();

        $asset->publicPath = '/remote';
        $found = ThemeAsset::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNotNull($found);
        self::assertEquals('/remote', $asset->publicPath);
    }

    public function testCreate(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->getIdAsInt();
        $asset->name = 'Test';
        $asset->create();

        self::assertNotNull(ThemeAsset::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testFormat(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->getIdAsInt();
        $asset->name = 'Test';
        $asset->create();

        self::assertEquals([], $asset->format());
    }
}
