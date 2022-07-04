<?php

namespace Jinya\Tests\Database;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\UploadingFile;
use App\Database\UploadingFileChunk;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UploadingFileTest extends TestCase
{

    private File $testFile;

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        UploadingFile::findByKeyword('');
    }

    public function testFormat(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        self::assertEquals($uploadingFile->format(), []);
    }

    public function testFindByFile(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $found = UploadingFile::findByFile($this->testFile->getIdAsInt());
        self::assertEquals($uploadingFile->id, $found->id);
    }

    public function testFindByFileNonExistent(): void
    {
        self::assertNull(UploadingFile::findByFile(-1));
    }

    public function testCreate(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $result = UploadingFile::findByFile($uploadingFile->fileId);
        self::assertEquals($result->id, $uploadingFile->id);
    }

    public function testCreateDouble(): void
    {
        $this->expectException(UniqueFailedException::class);
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $uploadingFile2 = new UploadingFile();
        $uploadingFile2->fileId = $this->testFile->getIdAsInt();
        $uploadingFile2->create();
    }

    public function testCreateFileIdNotGiven(): void
    {
        $this->expectError();
        $uploadingFile = new UploadingFile();
        $uploadingFile->create();
    }

    public function testGetIdAsString(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->id = 'Test';

        self::assertEquals('Test', $uploadingFile->getIdAsString());
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        UploadingFile::findAll();
    }

    public function testUpdate(): void
    {
        $this->expectException(RuntimeException::class);
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();
        $uploadingFile->update();
    }

    public function testGetFile(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $file = $uploadingFile->getFile();
        self::assertEquals($this->testFile->format(), $file->format());
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        UploadingFile::findById(0);
    }

    public function testDelete(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $uploadingFile->delete();

        $found = UploadingFile::findByFile($this->testFile->getIdAsInt());
        self::assertNull($found);
    }

    public function testGetChunks(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $uploadingFileChunk = new UploadingFileChunk();
        $uploadingFileChunk->chunkPath = '';
        $uploadingFileChunk->uploadingFileId = $uploadingFile->getIdAsString();
        $uploadingFileChunk->chunkPosition = 0;
        $uploadingFileChunk->create();

        $chunks = $uploadingFile->getChunks();
        self::assertCount(1, $chunks);

        $chunks = $uploadingFile->getChunks();
        self::assertEquals($uploadingFileChunk->format(), $chunks->current()->format());
    }

    public function testGetChunksNone(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->getIdAsInt();
        $uploadingFile->create();

        $chunks = $uploadingFile->getChunks();
        self::assertEquals(0, iterator_count($chunks));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createFile();
    }

    private function createFile(): void
    {
        $file = new File();
        $file->name = Uuid::uuid();

        $file->create();
        $this->testFile = $file;
    }
}
