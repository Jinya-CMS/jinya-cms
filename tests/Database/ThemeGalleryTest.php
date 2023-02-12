<?php

namespace Jinya\Tests\Database;

use App\Database\Gallery;
use App\Database\ThemeGallery;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeGalleryTest extends ThemeTestCase
{
    private Gallery $gallery;

    public function testFindByThemeNoGallery(): void
    {
        $themeGalleries = ThemeGallery::findByTheme($this->theme->getIdAsInt());
        self::assertCount(0, iterator_to_array($themeGalleries));
    }

    public function testFindByTheme(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
        $themeGallery->name = 'Test';
        $themeGallery->create();

        $themeGalleries = ThemeGallery::findByTheme($this->theme->getIdAsInt());
        self::assertCount(1, iterator_to_array($themeGalleries));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeGallery::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
        $themeGallery->name = 'Test';
        $themeGallery->create();

        $found = ThemeGallery::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertEquals($themeGallery->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
        $themeGallery->name = 'Test';
        $themeGallery->create();

        self::assertNotNull(ThemeGallery::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));

        $themeGallery->delete();
        self::assertNull(ThemeGallery::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testUpdate(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
        $themeGallery->name = 'Test';
        $themeGallery->create();

        $gallery = new Gallery();
        $gallery->name = 'Tempgallery';
        $gallery->create();

        $themeGallery->galleryId = $gallery->getIdAsInt();
        $themeGallery->update();
        $found = ThemeGallery::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNotNull($found);
        self::assertEquals($gallery->getIdAsInt(), $found->galleryId);
    }

    public function testCreate(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
        $themeGallery->name = 'Test';
        $themeGallery->create();

        self::assertNotNull(ThemeGallery::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testGalleryFormat(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
        $themeGallery->name = 'Test';
        $themeGallery->create();

        self::assertEquals([
                               'name' => 'Test',
                               'gallery' => $this->gallery->format(),
        ], $themeGallery->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $this->gallery = $gallery;
    }
}
