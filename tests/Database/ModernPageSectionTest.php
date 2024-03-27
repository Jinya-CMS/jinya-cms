<?php

namespace Jinya\Tests\Database;

use App\Database\File;
use App\Database\Gallery;
use App\Database\ModernPage;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;

class ModernPageSectionTest extends DatabaseAwareTestCase
{
    public function testFormatHtml(): void
    {
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, execute: 'Test', html: 'Test');
        $this->assertEquals([
            'position' => $section->position,
            'id' => $section->id,
            'html' => $section->html,
        ], $section->format());
    }

    public function testFormatFile(): void
    {
        $file = $this->createFile();
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, execute: $file->id, html: null, galleryId: null, fileId: $file->id);
        $this->assertEquals([
            'position' => $section->position,
            'id' => $section->id,
            'file' => [
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
                'path' => $file->path,
            ],
        ], $section->format());
    }

    public function testGetFile(): void
    {
        $file = $this->createFile();
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, execute: $file->id, html: null, galleryId: null, fileId: $file->id);
        $this->assertEquals($file->name, $section->getFile()->name);
        self::assertNull($section->getGallery());
    }

    private function createFile(): File
    {
        $file = new File();
        $file->path = 'this-does-not-exist';
        $file->name = Uuid::uuid();
        $file->type = 'application/octet-stream';
        $file->create();

        return $file;
    }

    public function testFormatGallery(): void
    {
        $gallery = $this->createGallery();
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, execute: $gallery->id, html: null, galleryId: $gallery->id);
        $this->assertEquals([
            'position' => $section->position,
            'id' => $section->id,
            'gallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'type' => $gallery->type,
                'orientation' => $gallery->orientation,
            ],
        ], $section->format());
    }

    public function testGetGallery(): void
    {
        $gallery = $this->createGallery();
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, execute: $gallery->id, html: null, galleryId: $gallery->id);
        $this->assertEquals($gallery->name, $section->getGallery()->name);
        self::assertNull($section->getFile());
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->description = Uuid::uuid();
        $gallery->create();

        return $gallery;
    }

    public function testGetSectionPage(): void
    {
        $page = $this->createSectionPage();
        $section = $this->createSection(1, $page->id);

        $sectionPage = $section->getSectionPage();
        $this->assertEquals($page->format(), $sectionPage->format());
    }

    private function createSectionPage(): ModernPage
    {
        $page = new ModernPage();
        $page->name = Uuid::uuid();
        $page->create();

        return $page;
    }

    private function createSection(
        int $position,
        int $pageId,
        bool $execute = true,
        string $html = null,
        int $galleryId = null,
        int $fileId = null
    ): Section {
        $section = new Section();
        $section->id = 0;
        $section->pageId = $pageId;
        $section->html = $html;
        $section->galleryId = $galleryId;
        $section->fileId = $fileId;
        $section->position = $position;

        return $section;
    }
}
