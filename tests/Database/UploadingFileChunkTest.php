<?php

namespace Jinya\Tests\Database;

use App\Database\File;
use App\Database\UploadingFile;
use App\Database\UploadingFileChunk;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UploadingFileChunkTest extends TestCase
{

    private File $testFile;
    private UploadingFile $testUploadingFile;

    public function testUpdate(): void
    {
        $this->expectException(RuntimeException::class);

        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->getIdAsString();
        $chunk->chunkPath = '';
        $chunk->create();

        $chunk->update();
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        UploadingFileChunk::findById(0);
    }

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        UploadingFileChunk::findByKeyword('');
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        UploadingFileChunk::findAll();
    }

    public function testDelete(): void
    {
        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->getIdAsString();
        $chunk->chunkPath = '';
        $chunk->create();
        $chunk->delete();

        $chunks = UploadingFileChunk::findByFile($this->testFile->getIdAsInt());
        self::assertCount(0, $chunks);
    }

    public function testCreate(): void
    {
        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->getIdAsString();
        $chunk->chunkPath = '';
        $chunk->create();

        $chunks = UploadingFileChunk::findByFile($this->testFile->getIdAsInt());
        self::assertCount(1, $chunks);
    }

    public function testFindByFile(): void
    {
        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->getIdAsString();
        $chunk->chunkPath = '';
        $chunk->create();

        $chunks = UploadingFileChunk::findByFile($this->testFile->getIdAsInt());
        self::assertCount(1, $chunks);
    }

    public function testFindByFileNonExistent(): void
    {
        $chunks = UploadingFileChunk::findByFile(-1);
        self::assertCount(0, $chunks);
    }

    public function testFormat(): void
    {
        $chunk = new UploadingFileChunk();
        $chunk->chunkPosition = 0;
        $chunk->uploadingFileId = $this->testUploadingFile->getIdAsString();
        $chunk->chunkPath = '';
        $chunk->create();

        self::assertEquals([], $chunk->format());
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
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $this->testUploadingFile = $uploadingFile;
    }
}
