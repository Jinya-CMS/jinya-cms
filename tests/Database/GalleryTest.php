<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\UuidGenerator;
use PDOException;

class GalleryTest extends DatabaseAwareTestCase
{
    private function createGallery(string $name = 'Gallery', string $description = '', bool $execute = true): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = $name;
        $gallery->description = $description;
        if ($execute) {
            $gallery->create();
        }

        return $gallery;
    }

    private function createPosition(int $position, int $gallery): void
    {
        $file = new File();
        $file->path = 'this-does-not-exist';
        $file->name = UuidGenerator::generateV4();
        $file->type = 'application/octet-stream';
        $file->create();

        $filePosition = new GalleryFilePosition();
        $filePosition->position = $position;
        $filePosition->fileId = $file->id;
        $filePosition->galleryId = $gallery;
        $filePosition->create();
    }

    public function testGetFiles(): void
    {
        $gallery = $this->createGallery();
        $this->createPosition(1, $gallery->id);
        $this->createPosition(2, $gallery->id);
        $this->createPosition(3, $gallery->id);
        $this->createPosition(4, $gallery->id);
        $this->createPosition(5, $gallery->id);

        $this->assertCount(5, iterator_to_array($gallery->getFiles()));
    }

    public function testGetFilesNone(): void
    {
        $gallery = $this->createGallery();

        $this->assertCount(0, iterator_to_array($gallery->getFiles()));
    }

    public function testGetCreator(): void
    {
        $gallery = $this->createGallery();
        $this->assertEquals(CurrentUser::$currentUser, $gallery->getCreator());
    }

    public function testCreate(): void
    {
        $gallery = $this->createGallery(execute: false);
        $gallery->create();

        $foundGallery = Gallery::findById($gallery->id);
        $this->assertEquals($gallery->format(), $foundGallery->format());
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $this->createGallery();
        $gallery = $this->createGallery(execute: false);
        $gallery->create();
    }

    public function testUpdate(): void
    {
        $gallery = $this->createGallery();
        $gallery->name = 'New name';
        $gallery->update();

        $foundGallery = Gallery::findById($gallery->id);
        $this->assertEquals('New name', $foundGallery->name);
    }

    public function testUpdateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $this->createGallery(name: 'New name');
        $gallery = $this->createGallery();
        $gallery->name = 'New name';
        $gallery->update();
    }

    public function testUpdateNotExisting(): void
    {
        $gallery = $this->createGallery();
        $gallery->delete();

        $gallery->name = 'New name';
        $gallery->update();

        $foundGallery = Gallery::findById($gallery->id);
        $this->assertNull($foundGallery);
    }

    public function testFormat(): void
    {
        $gallery = $this->createGallery();
        $formattedGallery = $gallery->format();
        $this->assertArrayHasKey('id', $formattedGallery);
        $this->assertArrayHasKey('name', $formattedGallery);
        $this->assertArrayHasKey('description', $formattedGallery);
        $this->assertArrayHasKey('type', $formattedGallery);
        $this->assertArrayHasKey('orientation', $formattedGallery);
        $this->assertArrayHasKey('created', $formattedGallery);
        $this->assertArrayHasKey('updated', $formattedGallery);
    }

    public function testGetUpdatedBy(): void
    {
        $gallery = $this->createGallery();
        $this->assertEquals(CurrentUser::$currentUser, $gallery->getUpdatedBy());
    }

    public function testDelete(): void
    {
        $gallery = $this->createGallery();
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->id);
        $this->assertNull($foundGallery);
    }

    public function testDeleteWithGalleryFilePositions(): void
    {
        $gallery = $this->createGallery();
        $this->createPosition(1, $gallery->id);
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->id);
        $this->assertNull($foundGallery);
    }

    public function testDeleteNotExisting(): void
    {
        $gallery = $this->createGallery();
        $gallery->delete();
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->id);
        $this->assertNull($foundGallery);
    }

    public function testFindAll(): void
    {
        $this->createGallery();
        $this->createGallery(UuidGenerator::generateV4());
        $this->createGallery(UuidGenerator::generateV4());

        $all = Gallery::findAll();
        $this->assertCount(3, iterator_to_array($all));
    }

    public function testFindById(): void
    {
        $gallery = $this->createGallery();

        $foundGallery = Gallery::findById($gallery->id);
        $this->assertEquals($gallery->format(), $foundGallery->format());
    }
}
