<?php

namespace Jinya\Tests\Database;

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

    public function testGetSegmentPage(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(1, $page->getIdAsInt());

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

    private function createSegment(int $position, int $pageId, bool $execute = true, string $html = null, int $galleryId = null, int $fileId = null, int $formId = null): Segment
    {
        $segment = new Segment();
        $segment->pageId = $pageId;
        $segment->html = $html;
        $segment->galleryId = $galleryId;
        $segment->fileId = $fileId;
        $segment->position = $position;
        $segment->formId = $formId;
        if ($execute) {
            $segment->create();
        }

        return $segment;
    }

    public function testUpdate(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt());
        $segment->position = 5;
        $segment->update();
        $foundSegment = Segment::findByPosition($page->getIdAsInt(), 5);
        $this->assertEquals($segment, $foundSegment);
    }

    public function testUpdateNotExistent(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), execute: false);
        $segment->position = 5;
        $segment->update();
        $foundSegment = Segment::findByPosition($page->getIdAsInt(), 5);
        $this->assertNull($foundSegment);
    }

    public function testCreate(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), execute: false);
        $segment->create();
        $foundSegment = Segment::findByPosition($page->getIdAsInt(), 0);
        $this->assertEquals($segment, $foundSegment);
    }

    public function testFindByPosition(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt());
        $this->createSegment(1, $page->getIdAsInt());
        $foundSegment = Segment::findByPosition($page->getIdAsInt(), 0);
        $this->assertEquals($segment, $foundSegment);
    }

    public function testFormatHtml(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), html: 'Test');
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->getIdAsInt(),
            'html' => $segment->html,
        ], $segment->format());
    }

    public function testFormatFile(): void
    {
        $file = $this->createFile();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), fileId: $file->getIdAsInt());
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->getIdAsInt(),
            'file' => [
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
                'path' => $file->path,
            ],
            'target' => $segment->target,
            'action' => $segment->action,
            'script' => $segment->script,
        ], $segment->format());
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
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), fileId: $file->getIdAsInt());
        $this->assertEquals($file->name, $segment->getFile()->name);
        self::assertNull($segment->getGallery());
        self::assertNull($segment->getForm());
    }

    public function testFormatGallery(): void
    {
        $gallery = $this->createGallery();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), galleryId: $gallery->getIdAsInt());
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->getIdAsInt(),
            'gallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'type' => $gallery->type,
                'orientation' => $gallery->orientation,
            ],
        ], $segment->format());
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->description = Uuid::uuid();
        $gallery->create();

        return $gallery;
    }

    public function testFormatForm(): void
    {
        $form = $this->createForm();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), formId: $form->getIdAsInt());
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->getIdAsInt(),
            'form' => [
                'id' => $form->getIdAsInt(),
                'title' => $form->title,
                'description' => $form->description,
                'toAddress' => $form->toAddress,
            ],
        ], $segment->format());
    }

    private function createForm(): Form
    {
        $form = new Form();
        $form->title = 'Form';
        $form->toAddress = 'test@example.com';
        $form->create();

        return $form;
    }

    public function testGetGallery(): void
    {
        $gallery = $this->createGallery();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), galleryId: $gallery->getIdAsInt());
        $this->assertEquals($gallery->name, $segment->getGallery()->name);
        self::assertNull($segment->getFile());
        self::assertNull($segment->getForm());
    }

    public function testGetForm(): void
    {
        $form = $this->createForm();
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), formId: $form->getIdAsInt());
        $this->assertEquals($form->title, $segment->getForm()->title);
        self::assertNull($segment->getFile());
        self::assertNull($segment->getGallery());
    }

    public function testDelete(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt());
        $segment->delete();
        $foundSegment = Segment::findByPosition($page->getIdAsInt(), 5);
        $this->assertNull($foundSegment);
    }

    public function testDeleteNotExistent(): void
    {
        $page = $this->createSegmentPage();
        $segment = $this->createSegment(0, $page->getIdAsInt(), execute: false);
        $segment->delete();
        $foundSegment = Segment::findByPosition($page->getIdAsInt(), 5);
        $this->assertNull($foundSegment);
    }

    public function testMove(): void
    {
        $page = $this->createSegmentPage();
        $segment1 = $this->createSegment(0, $page->getIdAsInt());
        $segment2 = $this->createSegment(1, $page->getIdAsInt());
        $segment3 = $this->createSegment(2, $page->getIdAsInt());
        $segment2->move(0);

        $foundSegment1 = Segment::findByPosition($page->getIdAsInt(), 0);
        $foundSegment2 = Segment::findByPosition($page->getIdAsInt(), 1);
        $foundSegment3 = Segment::findByPosition($page->getIdAsInt(), 2);

        $this->assertEquals($foundSegment1->getIdAsInt(), $segment2->getIdAsInt());
        $this->assertEquals($foundSegment2->getIdAsInt(), $segment1->getIdAsInt());
        $this->assertEquals($foundSegment3->getIdAsInt(), $segment3->getIdAsInt());
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        Segment::findById(0);
    }

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        Segment::findByKeyword('test');
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        Segment::findAll();
    }
}
