<?php

namespace Jinya\Tests\Database;

use App\Database\SimplePage;
use App\Database\ThemePage;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemePageTest extends ThemeTestCase
{
    private SimplePage $simplePage;

    public function testFindByThemeNoSimplePage(): void
    {
        $themePages = ThemePage::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($themePages));
    }

    public function testFindByTheme(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $themePages = ThemePage::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($themePages));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemePage::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $found = ThemePage::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($themePage->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        self::assertNotNull(ThemePage::findByThemeAndName($this->theme->id, 'Test'));

        $themePage->delete();
        self::assertNull(ThemePage::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $simplePage = new SimplePage();
        $simplePage->title = 'TempSimplePage';
        $simplePage->content = Uuid::uuid();
        $simplePage->create();

        $themePage->pageId = $simplePage->id;
        $themePage->update();
        $found = ThemePage::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($simplePage->id, $found->pageId);
    }

    public function testCreate(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        self::assertNotNull(ThemePage::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testSimplePageat(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        self::assertEquals([
            'name' => 'Test',
            'page' => $this->simplePage->format(),
        ], $themePage->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $simplePage = new SimplePage();
        $simplePage->title = Uuid::uuid();
        $simplePage->content = Uuid::uuid();
        $simplePage->create();

        $this->simplePage = $simplePage;
    }
}
