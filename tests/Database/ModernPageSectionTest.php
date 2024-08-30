<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;

class ModernPageSectionTest extends DatabaseAwareTestCase
{
    public function testFormatHtml(): void
    {
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, html: 'Test');
        self::assertEquals([
            'position' => $section->position,
            'id' => $section->id,
            'html' => $section->html,
        ], $section->format());
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
        string $html = null,
        int $galleryId = null,
        int $fileId = null
    ): ModernPageSection {
        $section = new ModernPageSection();
        $section->id = 0;
        $section->pageId = $pageId;
        $section->html = $html;
        $section->galleryId = $galleryId;
        $section->fileId = $fileId;
        $section->position = $position;

        return $section;
    }

    public function testFormatFile(): void
    {
        $file = $this->createFile();
        $page = $this->createSectionPage();
        $section = $this->createSection(
            0,
            $page->id,
            html: $file->id,
            fileId: $file->id
        );
        self::assertEquals([
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

    private function createFile(): File
    {
        $file = new File();
        $file->path = 'this-does-not-exist';
        $file->name = Uuid::uuid();
        $file->type = 'application/octet-stream';
        $file->create();

        return $file;
    }

    public function testGetFile(): void
    {
        $file = $this->createFile();
        $page = $this->createSectionPage();
        $section = $this->createSection(
            0,
            $page->id,
            html: $file->id,
            fileId: $file->id
        );
        self::assertEquals($file->name, $section->getFile()->name);
        self::assertNull($section->getGallery());
    }

    public function testFormatGallery(): void
    {
        $gallery = $this->createGallery();
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, html: $gallery->id, galleryId: $gallery->id);
        self::assertEquals([
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

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->description = Uuid::uuid();
        $gallery->create();

        return $gallery;
    }

    public function testGetGallery(): void
    {
        $gallery = $this->createGallery();
        $page = $this->createSectionPage();
        $section = $this->createSection(0, $page->id, html: $gallery->id, galleryId: $gallery->id);
        self::assertEquals($gallery->name, $section->getGallery()->name);
        self::assertNull($section->getFile());
    }

    public function testGetSectionPage(): void
    {
        $page = $this->createSectionPage();
        $section = $this->createSection(1, $page->id);

        $sectionPage = $section->getModernPage();
        self::assertEquals($page->format(), $sectionPage->format());
    }
}
