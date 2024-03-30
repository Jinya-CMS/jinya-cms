<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Database\ModernPage;
use Jinya\Cms\Database\ThemeModernPage;
use Jinya\Cms\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeModernPageTest extends ThemeTestCase
{
    private ModernPage $modernPage;

    public function testFindByThemeNoModernPage(): void
    {
        $themeModernPages = ThemeModernPage::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($themeModernPages));
    }

    public function testFindByTheme(): void
    {
        $themeModernPage = new ThemeModernPage();
        $themeModernPage->modernPageId = $this->modernPage->id;
        $themeModernPage->themeId = $this->theme->id;
        $themeModernPage->name = 'Test';
        $themeModernPage->create();

        $themeModernPages = ThemeModernPage::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($themeModernPages));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeModernPage::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeModernPage = new ThemeModernPage();
        $themeModernPage->modernPageId = $this->modernPage->id;
        $themeModernPage->themeId = $this->theme->id;
        $themeModernPage->name = 'Test';
        $themeModernPage->create();

        $found = ThemeModernPage::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($themeModernPage->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeModernPage = new ThemeModernPage();
        $themeModernPage->modernPageId = $this->modernPage->id;
        $themeModernPage->themeId = $this->theme->id;
        $themeModernPage->name = 'Test';
        $themeModernPage->create();

        self::assertNotNull(ThemeModernPage::findByThemeAndName($this->theme->id, 'Test'));

        $themeModernPage->delete();
        self::assertNull(ThemeModernPage::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $themeModernPage = new ThemeModernPage();
        $themeModernPage->modernPageId = $this->modernPage->id;
        $themeModernPage->themeId = $this->theme->id;
        $themeModernPage->name = 'Test';
        $themeModernPage->create();

        $modernPage = new ModernPage();
        $modernPage->name = 'TempModernPage';
        $modernPage->create();

        $themeModernPage->modernPageId = $modernPage->id;
        $themeModernPage->update();
        $found = ThemeModernPage::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($modernPage->id, $found->modernPageId);
    }

    public function testCreate(): void
    {
        $themeModernPage = new ThemeModernPage();
        $themeModernPage->modernPageId = $this->modernPage->id;
        $themeModernPage->themeId = $this->theme->id;
        $themeModernPage->name = 'Test';
        $themeModernPage->create();

        self::assertNotNull(ThemeModernPage::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testCreateModernPage(): void
    {
        $themeModernPage = new ThemeModernPage();
        $themeModernPage->modernPageId = $this->modernPage->id;
        $themeModernPage->themeId = $this->theme->id;
        $themeModernPage->name = 'Test';
        $themeModernPage->create();

        self::assertEquals([
            'name' => 'Test',
            'modernPage' => $this->modernPage->format(),
        ], $themeModernPage->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $modernPage = new ModernPage();
        $modernPage->name = Uuid::uuid();
        $modernPage->create();

        $this->modernPage = $modernPage;
    }
}
