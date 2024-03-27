<?php

namespace Jinya\Tests\Database;

use App\Database\ClassicPage;
use App\Database\ThemeClassicPage;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeClassicPageTest extends ThemeTestCase
{
    private ClassicPage $classicPage;

    public function testFindByThemeNoSimplePage(): void
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

        $simplePage = new ClassicPage();
        $simplePage->title = 'TempClassicPage';
        $simplePage->content = Uuid::uuid();
        $simplePage->create();

        $themePage->classicPageId = $simplePage->id;
        $themePage->update();
        $found = ThemeClassicPage::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($simplePage->id, $found->classicPageId);
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

    public function testSimplePageat(): void
    {
        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $this->classicPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        self::assertEquals([
            'name' => 'Test',
            'page' => $this->classicPage->format(),
        ], $themePage->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $simplePage = new ClassicPage();
        $simplePage->title = Uuid::uuid();
        $simplePage->content = Uuid::uuid();
        $simplePage->create();

        $this->classicPage = $simplePage;
    }
}
