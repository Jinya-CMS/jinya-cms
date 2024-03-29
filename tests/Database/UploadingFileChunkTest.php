<?php

namespace Jinya\Tests\Database;

use App\Database\File;
use App\Database\UploadingFile;
use App\Database\UploadingFileChunk;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;

class UploadingFileChunkTest extends DatabaseAwareTestCase
{
    private File $testFile;
    private UploadingFile $testUploadingFile;

    public function testDelete(): void
    {
        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->id;
        $chunk->chunkPath = '';
        $chunk->create();
        $chunk->delete();

        $chunks = UploadingFileChunk::findByFile($this->testFile->id);
        self::assertCount(0, iterator_to_array($chunks));
    }

    public function testCreate(): void
    {
        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->id;
        $chunk->chunkPath = '';
        $chunk->create();

        $chunks = UploadingFileChunk::findByFile($this->testFile->id);
        self::assertCount(1, iterator_to_array($chunks));
    }

    public function testFindByFile(): void
    {
        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->id;
        $chunk->chunkPath = '';
        $chunk->create();

        $chunks = UploadingFileChunk::findByFile($this->testFile->id);
        self::assertCount(1, iterator_to_array($chunks));
    }

    public function testFindByFileNonExistent(): void
    {
        $chunks = UploadingFileChunk::findByFile(-1);
        self::assertCount(0, iterator_to_array($chunks));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createFile();
        $this->createUploadingFile();
    }

    private function createFile(): void
    {
        $file = new File();
        $file->name = Uuid::uuid();

        $file->create();
        $this->testFile = $file;
    }

    private function createUploadingFile(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
        $uploadingFile->create();

        $this->testUploadingFile = $uploadingFile;
    }
}
