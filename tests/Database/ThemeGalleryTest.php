<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeGalleryTest extends ThemeTestCase
{
    private Gallery $gallery;

    public function testFindByThemeNoGallery(): void
    {
        $themeGalleries = ThemeGallery::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($themeGalleries));
    }

    public function testFindByTheme(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->name = 'Test';
        $themeGallery->create();

        $themeGalleries = ThemeGallery::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($themeGalleries));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeGallery::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->name = 'Test';
        $themeGallery->create();

        $found = ThemeGallery::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($themeGallery->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->name = 'Test';
        $themeGallery->create();

        self::assertNotNull(ThemeGallery::findByThemeAndName($this->theme->id, 'Test'));

        $themeGallery->delete();
        self::assertNull(ThemeGallery::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->name = 'Test';
        $themeGallery->create();

        $gallery = new Gallery();
        $gallery->name = 'Tempgallery';
        $gallery->create();

        $themeGallery->galleryId = $gallery->id;
        $themeGallery->update();
        $found = ThemeGallery::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($gallery->id, $found->galleryId);
    }

    public function testCreate(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->name = 'Test';
        $themeGallery->create();

        self::assertNotNull(ThemeGallery::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testGalleryFormat(): void
    {
        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $this->gallery->id;
        $themeGallery->themeId = $this->theme->id;
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
