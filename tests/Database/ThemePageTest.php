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
        $themePages = ThemePage::findByTheme($this->theme->getIdAsInt());
        self::assertCount(0, $themePages);
    }

    public function testFindByTheme(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
        $themePage->name = 'Test';
        $themePage->create();

        $themePages = ThemePage::findByTheme($this->theme->getIdAsInt());
        self::assertCount(1, $themePages);
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemePage::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
        $themePage->name = 'Test';
        $themePage->create();

        $found = ThemePage::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertEquals($themePage->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
        $themePage->name = 'Test';
        $themePage->create();

        self::assertNotNull(ThemePage::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));

        $themePage->delete();
        self::assertNull(ThemePage::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testUpdate(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
        $themePage->name = 'Test';
        $themePage->create();

        $simplePage = new SimplePage();
        $simplePage->title = 'TempSimplePage';
        $simplePage->content = Uuid::uuid();
        $simplePage->create();

        $themePage->pageId = $simplePage->getIdAsInt();
        $themePage->update();
        $found = ThemePage::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNotNull($found);
        self::assertEquals($simplePage->getIdAsInt(), $found->pageId);
    }

    public function testCreate(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
        $themePage->name = 'Test';
        $themePage->create();

        self::assertNotNull(ThemePage::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testSimplePageat(): void
    {
        $themePage = new ThemePage();
        $themePage->pageId = $this->simplePage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
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
