<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;

class FileControllerTest extends DatabaseAwareTestCase
{
    private function getController(string $body): FileController
    {
        $controller = new FileController();
        $controller->request = (new ServerRequest('', ''))->withBody(Stream::create($body));

        return $controller;
    }

    public function testGetFileContent(): void
    {
        $path = Uuid::uuid();
        $file = new File();
        $file->name = 'Test';
        $file->path = StorageBaseService::WEB_PATH . $path;
        $file->type = 'text/plain';
        $file->create();
        file_put_contents(StorageBaseService::SAVE_PATH . $path, 'Test');

        $controller = $this->getController('');
        $result = $controller->getFileContent($file->id);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($file->type, $result->getHeaderLine('Content-Type'));
        self::assertEquals('Test', $result->getBody()->getContents());

        unlink(StorageBaseService::SAVE_PATH . $path);
    }

    public function testGetFileContentFileNotFound(): void
    {
        $controller = $this->getController('');
        $result = $controller->getFileContent(-1);
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

    public function testGetFileContentFileNoContentFound(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $controller = $this->getController('');
        $result = $controller->getFileContent($file->id);

        self::assertEquals(404, $result->getStatusCode());
    }

    public function testStartUpload(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $controller = $this->getController('');
        $result = $controller->startUpload($file->id);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function testStartUploadDuplicateCreation(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $controller = $this->getController('');
        $result = $controller->startUpload($file->id);
        self::assertEquals(204, $result->getStatusCode());

        $result = $controller->startUpload($file->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(409, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Upload was started previously',
                'type' => 'uploaded-started',
            ],
        ], $body);
    }

    public function testStartUploadFileNotFound(): void
    {
        $controller = $this->getController('');
        $result = $controller->startUpload(-1);
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

    public function testFinishUpload(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $controller = $this->getController('');
        $result = $controller->startUpload($file->id);
        self::assertEquals(204, $result->getStatusCode());

        $result = $controller->finishUpload($file->id);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function testFinishUploadFileNotFound(): void
    {
        $controller = $this->getController('');
        $result = $controller->finishUpload(-1);
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

    public function testUploadChunk(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $controller = $this->getController('Test');
        $result = $controller->startUpload($file->id);
        self::assertEquals(204, $result->getStatusCode());

        $result = $controller->uploadChunk($file->id, 0);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUploadChunkFileNotFound(): void
    {
        $controller = $this->getController('');
        $result = $controller->uploadChunk(-1, 0);
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
}
