<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\File;
use Jinya\Cms\Database\Gallery;
use Jinya\Cms\Database\GalleryFilePosition;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Utils\UuidGenerator;
use Jinya\Database\Exception\UniqueFailedException;
use PDOException;

class GalleryTest extends DatabaseAwareTestCase
{
    public function testGetFiles(): void
    {
        $gallery = $this->createGallery();
        $this->createPosition(1, $gallery->id);
        $this->createPosition(2, $gallery->id);
        $this->createPosition(3, $gallery->id);
        $this->createPosition(4, $gallery->id);
        $this->createPosition(5, $gallery->id);

        self::assertCount(5, iterator_to_array($gallery->getFiles()));
    }

    private function createGallery(string $name = 'Gallery', bool $execute = true): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = $name;
        $gallery->description = '';
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

    public function testGetFilesNone(): void
    {
        $gallery = $this->createGallery();

        self::assertCount(0, iterator_to_array($gallery->getFiles()));
    }

    public function testGetCreator(): void
    {
        $gallery = $this->createGallery();
        self::assertEquals(CurrentUser::$currentUser, $gallery->getCreator());
    }

    public function testCreate(): void
    {
        $gallery = $this->createGallery(name: false, execute: false);
        $gallery->create();

        $foundGallery = Gallery::findById($gallery->id);
        self::assertEquals($gallery->format(), $foundGallery->format());
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(UniqueFailedException::class);
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
        self::assertEquals('New name', $foundGallery->name);
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
        self::assertNull($foundGallery);
    }

    public function testFormat(): void
    {
        $gallery = $this->createGallery();
        $formattedGallery = $gallery->format();
        self::assertArrayHasKey('id', $formattedGallery);
        self::assertArrayHasKey('name', $formattedGallery);
        self::assertArrayHasKey('description', $formattedGallery);
        self::assertArrayHasKey('type', $formattedGallery);
        self::assertArrayHasKey('orientation', $formattedGallery);
        self::assertArrayHasKey('created', $formattedGallery);
        self::assertArrayHasKey('updated', $formattedGallery);
    }

    public function testGetUpdatedBy(): void
    {
        $gallery = $this->createGallery();
        self::assertEquals(CurrentUser::$currentUser, $gallery->getUpdatedBy());
    }

    public function testDelete(): void
    {
        $gallery = $this->createGallery();
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->id);
        self::assertNull($foundGallery);
    }

    public function testDeleteWithGalleryFilePositions(): void
    {
        $gallery = $this->createGallery();
        $this->createPosition(1, $gallery->id);
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->id);
        self::assertNull($foundGallery);
    }

    public function testDeleteNotExisting(): void
    {
        $gallery = $this->createGallery();
        $gallery->delete();
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->id);
        self::assertNull($foundGallery);
    }

    public function testFindAll(): void
    {
        $this->createGallery();
        $this->createGallery(UuidGenerator::generateV4());
        $this->createGallery(UuidGenerator::generateV4());

        $all = Gallery::findAll();
        self::assertCount(3, iterator_to_array($all));
    }

    public function testFindById(): void
    {
        $gallery = $this->createGallery();

        $foundGallery = Gallery::findById($gallery->id);
        self::assertEquals($gallery->format(), $foundGallery->format());
    }
}
