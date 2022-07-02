<?php

namespace Jinya\Tests\Database;

use App\Database\File;
use App\Database\ThemeFile;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeFileTest extends ThemeTestCase
{
    private File $file;

    public function testFindByThemeNoFile(): void
    {
        $themeFiles = ThemeFile::findByTheme($this->theme->getIdAsInt());
        self::assertCount(0, $themeFiles);
    }

    public function testFindByTheme(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
        $themeFile->name = 'Test';
        $themeFile->create();

        $themeFiles = ThemeFile::findByTheme($this->theme->getIdAsInt());
        self::assertCount(1, $themeFiles);
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeFile::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
        $themeFile->name = 'Test';
        $themeFile->create();

        $found = ThemeFile::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertEquals($themeFile->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
        $themeFile->name = 'Test';
        $themeFile->create();

        self::assertNotNull(ThemeFile::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));

        $themeFile->delete();
        self::assertNull(ThemeFile::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testUpdate(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
        $themeFile->name = 'Test';
        $themeFile->create();

        $file = new File();
        $file->name = 'Tempfile';
        $file->create();

        $themeFile->fileId = $file->getIdAsInt();
        $themeFile->update();
        $found = ThemeFile::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNotNull($found);
        self::assertEquals($file->getIdAsInt(), $found->fileId);
    }

    public function testCreate(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
        $themeFile->name = 'Test';
        $themeFile->create();

        self::assertNotNull(ThemeFile::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testFormat(): void
    {
        $themeFile = new ThemeFile();
        $themeFile->fileId = $this->file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
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
