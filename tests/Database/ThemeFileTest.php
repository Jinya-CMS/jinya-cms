<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Database\File;
use Jinya\Cms\Database\ThemeFile;
use Jinya\Cms\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeFileTest extends ThemeTestCase
{
    private File $file;

    public function testFindByThemeNoFile(): void
    {
        $themeFiles = ThemeFile::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($themeFiles));
    }

    public function testFindByTheme(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->name = 'Test';
        $themeFile->create();

        $themeFiles = ThemeFile::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($themeFiles));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeFile::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->name = 'Test';
        $themeFile->create();

        $found = ThemeFile::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($themeFile->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->name = 'Test';
        $themeFile->create();

        self::assertNotNull(ThemeFile::findByThemeAndName($this->theme->id, 'Test'));

        $themeFile->delete();
        self::assertNull(ThemeFile::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->name = 'Test';
        $themeFile->create();

        $file = new File();
        $file->name = 'Tempfile';
        $file->create();

        $themeFile->fileId = $file->id;
        $themeFile->update();
        $found = ThemeFile::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($file->id, $found->fileId);
    }

    public function testCreate(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->name = 'Test';
        $themeFile->create();

        self::assertNotNull(ThemeFile::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testFormat(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->name = 'Test';
        $themeFile->create();

        self::assertEquals([
            'name' => 'Test',
            'file' => $this->file->format(),
        ], $themeFile->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $this->file = $file;
    }
}
