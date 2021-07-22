<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Utils\UuidGenerator;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
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

    private function createPosition(bool $position, int $gallery): void
    {
        $file = new File();
        $file->path = 'this-does-not-exist';
        $file->name = UuidGenerator::generateV4();
        $file->type = 'application/octet-stream';
        $file->create();

        $filePosition = new GalleryFilePosition();
        $filePosition->position = $position;
        $filePosition->fileId = $file->getIdAsInt();
        $filePosition->galleryId = $gallery;
        $filePosition->create();
    }

    public function testGetFiles(): void
    {
        $gallery = $this->createGallery();
        $this->createPosition(1, $gallery->getIdAsInt());
        $this->createPosition(2, $gallery->getIdAsInt());
        $this->createPosition(3, $gallery->getIdAsInt());
        $this->createPosition(4, $gallery->getIdAsInt());
        $this->createPosition(5, $gallery->getIdAsInt());

        $this->assertCount(5, $gallery->getFiles());
    }

    public function testGetFilesNone(): void
    {
        $gallery = $this->createGallery();

        $this->assertCount(0, $gallery->getFiles());
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

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
        $this->assertEquals($gallery->format(), $foundGallery->format());
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

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
        $this->assertEquals('New name', $foundGallery->name);
    }

    public function testUpdateUniqueFailed(): void
    {
        $this->expectException(UniqueFailedException::class);
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

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
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

    public function testFindByKeywordInName(): void
    {
        $this->createGallery('Test');
        $this->createGallery('Test2');
        $this->createGallery('Tes2t');
        $found = Gallery::findByKeyword('test');

        $this->assertCount(2, $found);
    }

    public function testFindByKeywordInDescription(): void
    {
        $this->createGallery('Test', description: 'Testgallery 1');
        $this->createGallery('Test2', description: 'Testgallery 12');
        $this->createGallery('Tes2t', description: 'Testgallery 2');
        $found = Gallery::findByKeyword('Testgallery 1');

        $this->assertCount(2, $found);
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

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
        $this->assertNull($foundGallery);
    }

    public function testDeleteWithGalleryFilePositions(): void
    {
        $gallery = $this->createGallery();
        $this->createPosition(1, $gallery->getIdAsInt());
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
        $this->assertNull($foundGallery);
    }

    public function testDeleteNotExisting(): void
    {
        $gallery = $this->createGallery();
        $gallery->delete();
        $gallery->delete();

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
        $this->assertNull($foundGallery);
    }

    public function testFindAll(): void
    {
        $this->createGallery();
        $this->createGallery(UuidGenerator::generateV4());
        $this->createGallery(UuidGenerator::generateV4());

        $all = Gallery::findAll();
        $this->assertCount(3, $all);
    }

    public function testFindById(): void
    {
        $gallery = $this->createGallery();

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
        $this->assertEquals($gallery->format(), $foundGallery->format());
    }

    public function testFindByIdNotSaved(): void
    {
        $gallery = $this->createGallery(execute: false);

        $foundGallery = Gallery::findById($gallery->getIdAsInt());
        $this->assertNull($foundGallery);
    }
}
