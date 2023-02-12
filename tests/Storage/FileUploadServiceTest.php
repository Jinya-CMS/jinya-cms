<?php

namespace Jinya\Tests\Storage;

use App\Database\Exceptions\EmptyResultException;
use App\Database\File;
use App\Database\UploadingFile;
use App\Storage\FileUploadService;
use App\Storage\StorageBaseService;
use App\Tests\DatabaseAwareTestCase;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

class FileUploadServiceTest extends DatabaseAwareTestCase
{
    private FileUploadService $service;
    private File $file;
    private UploadingFile $uploadingFile;

    public function testSaveChunk(): void
    {
        $this->service->saveChunk($this->file->getIdAsInt(), 0, 'Test\n');
        $this->service->saveChunk($this->file->getIdAsInt(), 2, 'Bar');
        $this->service->saveChunk($this->file->getIdAsInt(), 1, 'Foo\n');

        self::assertCount(3, iterator_to_array($this->uploadingFile->getChunks()));

        /** @var File $file */
        $file = $this->service->finishUpload($this->file->getIdAsInt());
        $content = file_get_contents(StorageBaseService::BASE_PATH . '/public/' . $file->path);
        self::assertEquals('Test\nFoo\nBar', $content);
        @unlink(StorageBaseService::BASE_PATH . '/public/' . $file->path);
    }

    public function testSaveChunkFileNotFound(): void
    {
        $this->expectException(EmptyResultException::class);
        $this->service->saveChunk(-1, 0, '');
    }

    public function testSaveChunkDataNull(): void
    {
        $this->expectException(RuntimeException::class);
        $this->service->saveChunk(-1, 0, null);
    }

    public function testFinishUploadFileNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->service->finishUpload(-1);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileUploadService();
        $file = new File();
        $file->path = '';
        $file->name = 'Testfile';
        $file->type = '';
        $file->create();
        $this->file = $file;
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $file->getIdAsInt();
        $uploadingFile->create();
        $this->uploadingFile = $uploadingFile;
    }
}
