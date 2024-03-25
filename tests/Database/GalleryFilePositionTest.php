<?php

namespace Jinya\Tests\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\UuidGenerator;
use PDOException;
use RuntimeException;

class GalleryFilePositionTest extends DatabaseAwareTestCase
{
    private Gallery $gallery;
    private File $file;
    private File $file2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gallery = new Gallery();
        $this->gallery->description = 'Test description';
        $this->gallery->name = 'Test Gallery';
        $this->gallery->create();

        $this->file = new File();
        $this->file->name = UuidGenerator::generateV4();
        $this->file->path = UuidGenerator::generateV4();
        $this->file->create();

        $this->file2 = new File();
        $this->file2->name = UuidGenerator::generateV4();
        $this->file2->path = UuidGenerator::generateV4();
        $this->file2->create();
    }

    private function createGalleryFilePosition(int $position = 0, int $file = -1, bool $execute = true): GalleryFilePosition
    {
        $galleryFilePosition = new GalleryFilePosition();
        $galleryFilePosition->position = $position;
        $galleryFilePosition->galleryId = $this->gallery->id;
        if ($file !== -1) {
            $galleryFilePosition->fileId = $file;
        } else {
            $galleryFilePosition->fileId = $this->file->id;
        }
        if ($execute) {
            $galleryFilePosition->create();
        }

        return $galleryFilePosition;
    }

    public function testGetGallery(): void
    {
        $galleryFilePosition = $this->createGalleryFilePosition();
        $gallery = $galleryFilePosition->getGallery();
        $this->assertEquals($this->gallery->format(), $gallery->format());
    }

    public function testUpdate(): void
    {
        $galleryFilePosition = $this->createGalleryFilePosition();
        $galleryFilePosition->fileId = $this->file2->id;
        $galleryFilePosition->update();

        $savedItem = GalleryFilePosition::findByPosition($this->gallery->id, $galleryFilePosition->position);
        $this->assertEquals($galleryFilePosition->fileId, $savedItem->fileId);
    }

    public function testUpdateNonExistent(): void
    {
        $galleryFilePosition = $this->createGalleryFilePosition();
        $galleryFilePosition->delete();
        $galleryFilePosition->update();

        $savedItem = GalleryFilePosition::findByPosition($this->gallery->id, $galleryFilePosition->position);
        $this->assertNull($savedItem);
    }

    public function testMove(): void
    {
        $this->createGalleryFilePosition(0, file: $this->file2->id);
        $this->createGalleryFilePosition(1, file: $this->file2->id);
        $this->createGalleryFilePosition(2);
        $galleryFilePosition = $this->createGalleryFilePosition(3);

        $galleryFilePosition->move(1);
        /** @var array<GalleryFilePosition> $items */
        $items = iterator_to_array($this->gallery->getFiles());
        $this->assertEquals($this->file2->id, $items[0]->fileId);
        $this->assertEquals($this->file->id, $items[1]->fileId);
        $this->assertEquals($this->file2->id, $items[2]->fileId);
        $this->assertEquals($this->file->id, $items[3]->fileId);
    }

    public function testMoveNotExistent(): void
    {
        $this->expectError();
        $this->createGalleryFilePosition(1, file: $this->file2->id);
        $this->createGalleryFilePosition(2);
        $this->createGalleryFilePosition(3, file: $this->file2->id);
        $galleryFilePosition = $this->createGalleryFilePosition(4, execute: false);

        $galleryFilePosition->move(2);
        /** @var array<GalleryFilePosition> $items */
        $items = iterator_to_array($this->gallery->getFiles());
        $this->assertEquals($this->file2->id, $items[0]->fileId);
        $this->assertEquals($this->file->id, $items[1]->fileId);
        $this->assertEquals($this->file2->id, $items[2]->fileId);
    }

    public function testCreate(): void
    {
        $galleryFilePosition = $this->createGalleryFilePosition(execute: false);
        $galleryFilePosition->create();
        $this->assertTrue(true);
    }

    public function testCreateGalleryNotExistent(): void
    {
        $this->expectException(PDOException::class);
        $galleryFilePosition = $this->createGalleryFilePosition(execute: false);
        $galleryFilePosition->galleryId = -1;
        $galleryFilePosition->create();
    }

    public function testCreateFileNotExistent(): void
    {
        $this->expectException(PDOException::class);
        $galleryFilePosition = $this->createGalleryFilePosition(execute: false);
        $galleryFilePosition->fileId = -1;
        $galleryFilePosition->create();
    }

    public function testFindByPosition(): void
    {
        $this->createGalleryFilePosition(0);
        $this->createGalleryFilePosition(1);
        $this->createGalleryFilePosition(2);
        $galleryFilePosition = $this->createGalleryFilePosition(3);

        $foundItem = GalleryFilePosition::findByPosition($this->gallery->id, 3);
        $this->assertEquals($galleryFilePosition, $foundItem);
    }

    public function testFindByPositionNotExisting(): void
    {
        $this->createGalleryFilePosition(1);
        $this->createGalleryFilePosition(2);
        $this->createGalleryFilePosition(3);
        $this->createGalleryFilePosition(4);

        $foundItem = GalleryFilePosition::findByPosition($this->gallery->id, 5);
        $this->assertNull($foundItem);
    }

    public function testFormat(): void
    {
        $galleryFilePosition = $this->createGalleryFilePosition();

        $this->assertEquals([
            'gallery' => [
                'id' => $galleryFilePosition->galleryId,
                'name' => $this->gallery->name,
                'description' => $this->gallery->description,
            ],
            'file' => [
                'path' => $this->file->path,
                'id' => $this->file->id,
                'name' => $this->file->name,
                'type' => $this->file->type,
            ],
            'id' => $galleryFilePosition->id,
            'position' => $galleryFilePosition->position,
        ], $galleryFilePosition->format());
    }

    public function testDelete(): void
    {
        $galleryFilePosition = $this->createGalleryFilePosition();
        $galleryFilePosition->delete();

        $savedItem = GalleryFilePosition::findByPosition($this->gallery->id, $galleryFilePosition->position);
        $this->assertNull($savedItem);
    }

    public function testDeleteNotExistent(): void
    {
        $galleryFilePosition = $this->createGalleryFilePosition();
        $galleryFilePosition->delete();
        $galleryFilePosition->delete();

        $savedItem = GalleryFilePosition::findByPosition($this->gallery->id, $galleryFilePosition->position);
        $this->assertNull($savedItem);
    }
}
