<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use PDOException;

class UploadingFileTest extends DatabaseAwareTestCase
{
    private File $testFile;

    public function testFindByFile(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
        $uploadingFile->create();

        $found = UploadingFile::findByFile($this->testFile->id);
        self::assertEquals($uploadingFile->id, $found->id);
    }

    public function testFindByFileNonExistent(): void
    {
        self::assertNull(UploadingFile::findByFile(-1));
    }

    public function testCreate(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
        $uploadingFile->create();

        $result = UploadingFile::findByFile($uploadingFile->fileId);
        self::assertEquals($result->id, $uploadingFile->id);
    }

    public function testCreateDouble(): void
    {
        $this->expectException(PDOException::class);
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
        $uploadingFile->create();

        $uploadingFile2 = new UploadingFile();
        $uploadingFile2->fileId = $this->testFile->id;
        $uploadingFile2->create();
    }

    public function testCreateFileIdNotGiven(): void
    {
        $this->expectError();
        $uploadingFile = new UploadingFile();
        $uploadingFile->create();
    }

    public function testGetFile(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
        $uploadingFile->create();

        $file = $uploadingFile->getFile();
        self::assertEquals($this->testFile->format(), $file->format());
    }

    public function testDelete(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
        $uploadingFile->create();

        $uploadingFile->delete();

        $found = UploadingFile::findByFile($this->testFile->id);
        self::assertNull($found);
    }

    public function testGetChunks(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
        $uploadingFile->create();

        $uploadingFileChunk = new UploadingFileChunk();
        $uploadingFileChunk->chunkPath = '';
        $uploadingFileChunk->uploadingFileId = $uploadingFile->id;
        $uploadingFileChunk->chunkPosition = 0;
        $uploadingFileChunk->create();

        $chunks = $uploadingFile->getChunks();
        self::assertCount(1, iterator_to_array($chunks));
    }

    public function testGetChunksNone(): void
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $this->testFile->id;
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
