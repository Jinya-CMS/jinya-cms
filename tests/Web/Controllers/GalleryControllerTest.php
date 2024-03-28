<?php

namespace Jinya\Tests\Web\Controllers;

use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\GalleryController;

class GalleryControllerTest extends DatabaseAwareTestCase
{
    private function getController(mixed $body): GalleryController
    {
        $controller = new GalleryController();
        $controller->body = $body;

        return $controller;
    }

    private function getPosition(): GalleryFilePosition
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $position = new GalleryFilePosition();
        $position->position = 0;
        $position->fileId = $file->id;
        $position->galleryId = $gallery->id;
        $position->create();

        return $position;
    }

    public function testCreatePosition(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $controller = $this->getController(['position' => 0, 'file' => $file->id]);
        $result = $controller->createPosition($gallery->id);

        self::assertEquals(201, $result->getStatusCode());
    }

    public function testCreatePositionFileNotFound(): void
    {
        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $controller = $this->getController(['position' => 0, 'file' => -1]);
        $result = $controller->createPosition($gallery->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'File not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testCreatePositionGalleryNotFound(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $controller = $this->getController(['position' => 0, 'file' => $file->id]);
        $result = $controller->createPosition(-1);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Gallery not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testDeletePosition(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $position = new GalleryFilePosition();
        $position->position = 0;
        $position->fileId = $file->id;
        $position->galleryId = $gallery->id;
        $position->create();

        $controller = $this->getController([]);
        $result = $controller->deletePosition($gallery->id, 0);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function testDeletePositionPositionNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->deletePosition(-1, 0);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Gallery file position not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testGetPositions(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $position = new GalleryFilePosition();
        $position->position = 0;
        $position->fileId = $file->id;
        $position->galleryId = $gallery->id;
        $position->create();

        $controller = $this->getController([]);
        $result = $controller->getPositions($gallery->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, $body);
    }

    public function testGetPositionsGalleryNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getPositions(-1);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Gallery not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdatePositionNoParameters(): void
    {
        $position = $this->getPosition();

        $controller = $this->getController([]);
        $result = $controller->updatePosition($position->galleryId, 0);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUpdatePositionNoFile(): void
    {
        $position = $this->getPosition();

        $controller = $this->getController(['newPosition' => 2]);
        $result = $controller->updatePosition($position->galleryId, 0);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUpdatePositionFileNotFound(): void
    {
        $position = $this->getPosition();

        $controller = $this->getController(['file' => -1]);
        $result = $controller->updatePosition($position->galleryId, 0);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'File not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdatePositionNoPosition(): void
    {
        $position = $this->getPosition();

        $controller = $this->getController(['file' => $position->fileId]);
        $result = $controller->updatePosition($position->galleryId, 0);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUpdatePositionPositionNotFound(): void
    {
        $position = $this->getPosition();

        $controller = $this->getController([]);
        $result = $controller->updatePosition(-1, -1);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Gallery file position not found',
                'type' => 'not-found',
            ],
        ], $body);
    }
}
