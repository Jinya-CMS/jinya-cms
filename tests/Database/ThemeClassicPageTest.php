<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Database\ClassicPage;
use Jinya\Cms\Database\ThemeClassicPage;
use Jinya\Cms\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeClassicPageTest extends ThemeTestCase
{
    private ClassicPage $classicPage;

    public function testFindByThemeNoClassicPage(): void
    {
        $themePages = ThemeClassicPage::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($themePages));
    }

    public function testFindByTheme(): void
    {
        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $this->classicPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $themePages = ThemeClassicPage::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($themePages));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeClassicPage::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $this->classicPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $found = ThemeClassicPage::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($themePage->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $this->classicPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        self::assertNotNull(ThemeClassicPage::findByThemeAndName($this->theme->id, 'Test'));

        $themePage->delete();
        self::assertNull(ThemeClassicPage::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $this->classicPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $classicPage = new ClassicPage();
        $classicPage->title = 'TempClassicPage';
        $classicPage->content = Uuid::uuid();
        $classicPage->create();

        $themePage->classicPageId = $classicPage->id;
        $themePage->update();
        $found = ThemeClassicPage::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($classicPage->id, $found->classicPageId);
    }

    public function testCreate(): void
    {
        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $this->classicPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        self::assertNotNull(ThemeClassicPage::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testCreateClassicPage(): void
    {
        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $this->classicPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        self::assertEquals([
            'name' => 'Test',
            'classicPage' => $this->classicPage->format(),
        ], $themePage->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $classicPage = new ClassicPage();
        $classicPage->title = Uuid::uuid();
        $classicPage->content = Uuid::uuid();
        $classicPage->create();

        $this->classicPage = $classicPage;
    }
}
