<?php

namespace Jinya\Tests\Database;

use App\Database\SegmentPage;
use App\Database\ThemeSegmentPage;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeSegmentPageTest extends ThemeTestCase
{
    private SegmentPage $segmentPage;

    public function testFindByThemeNoSegmentPage(): void
    {
        $themeSegmentPages = ThemeSegmentPage::findByTheme($this->theme->getIdAsInt());
        self::assertCount(0, iterator_to_array($themeSegmentPages));
    }

    public function testFindByTheme(): void
    {
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->segmentPageId = $this->segmentPage->getIdAsInt();
        $themeSegmentPage->themeId = $this->theme->getIdAsInt();
        $themeSegmentPage->name = 'Test';
        $themeSegmentPage->create();

        $themeSegmentPages = ThemeSegmentPage::findByTheme($this->theme->getIdAsInt());
        self::assertCount(1, iterator_to_array($themeSegmentPages));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeSegmentPage::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->segmentPageId = $this->segmentPage->getIdAsInt();
        $themeSegmentPage->themeId = $this->theme->getIdAsInt();
        $themeSegmentPage->name = 'Test';
        $themeSegmentPage->create();

        $found = ThemeSegmentPage::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertEquals($themeSegmentPage->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->segmentPageId = $this->segmentPage->getIdAsInt();
        $themeSegmentPage->themeId = $this->theme->getIdAsInt();
        $themeSegmentPage->name = 'Test';
        $themeSegmentPage->create();

        self::assertNotNull(ThemeSegmentPage::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));

        $themeSegmentPage->delete();
        self::assertNull(ThemeSegmentPage::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testUpdate(): void
    {
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->segmentPageId = $this->segmentPage->getIdAsInt();
        $themeSegmentPage->themeId = $this->theme->getIdAsInt();
        $themeSegmentPage->name = 'Test';
        $themeSegmentPage->create();

        $segmentPage = new SegmentPage();
        $segmentPage->name = 'TempsegmentPage';
        $segmentPage->create();

        $themeSegmentPage->segmentPageId = $segmentPage->getIdAsInt();
        $themeSegmentPage->update();
        $found = ThemeSegmentPage::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNotNull($found);
        self::assertEquals($segmentPage->getIdAsInt(), $found->segmentPageId);
    }

    public function testCreate(): void
    {
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->segmentPageId = $this->segmentPage->getIdAsInt();
        $themeSegmentPage->themeId = $this->theme->getIdAsInt();
        $themeSegmentPage->name = 'Test';
        $themeSegmentPage->create();

        self::assertNotNull(ThemeSegmentPage::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testSegmentPageat(): void
    {
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->segmentPageId = $this->segmentPage->getIdAsInt();
        $themeSegmentPage->themeId = $this->theme->getIdAsInt();
        $themeSegmentPage->name = 'Test';
        $themeSegmentPage->create();

        self::assertEquals([
            'name' => 'Test',
            'segmentPage' => $this->segmentPage->format(),
        ], $themeSegmentPage->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $this->segmentPage = $segmentPage;
    }
}
