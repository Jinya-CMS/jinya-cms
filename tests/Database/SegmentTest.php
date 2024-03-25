<?php

namespace Jinya\Tests\Database;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\BlogPostSegment;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use RuntimeException;

class SegmentTest extends DatabaseAwareTestCase
{
    public function testFormatHtml(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->id, execute: 'Test', html: 'Test');
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->id,
            'html' => $segment->html,
        ], $segment->format());
    }

    public function testFormatFile(): void
    {
        $file = $this->createFile();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->id, execute: $file->id, html: null, galleryId: null, fileId: $file->id);
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->id,
            'file' => [
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
                'path' => $file->path,
            ],
        ], $segment->format());
    }

    public function testGetFile(): void
    {
        $file = $this->createFile();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->id, execute: $file->id, html: null, galleryId: null, fileId: $file->id);
        $this->assertEquals($file->name, $segment->getFile()->name);
        self::assertNull($segment->getGallery());
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
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->id, execute: $gallery->id, html: null, galleryId: $gallery->id);
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->id,
            'gallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'type' => $gallery->type,
                'orientation' => $gallery->orientation,
            ],
        ], $segment->format());
    }

    public function testGetGallery(): void
    {
        $gallery = $this->createGallery();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->id, execute: $gallery->id, html: null, galleryId: $gallery->id);
        $this->assertEquals($gallery->name, $segment->getGallery()->name);
        self::assertNull($segment->getFile());
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->description = Uuid::uuid();
        $gallery->create();

        return $gallery;
    }

    public function testGetSegmentPage(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(1, $page->id);

        $segmentPage = $segment->getSegmentPage();
        $this->assertEquals($page->format(), $segmentPage->format());
    }

    private function createSegmentPage(): SegmentPage
    {
        $page = new SegmentPage();
        $page->name = Uuid::uuid();
        $page->create();

        return $page;
    }

    private function createSegment(
        int $position,
        int $pageId,
        bool $execute = true,
        string $html = null,
        int $galleryId = null,
        int $fileId = null
    ): Segment
    {
        $segment = new Segment();
        $segment->id = 0;
        $segment->pageId = $pageId;
        $segment->html = $html;
        $segment->galleryId = $galleryId;
        $segment->fileId = $fileId;
        $segment->position = $position;

        return $segment;
    }
}
