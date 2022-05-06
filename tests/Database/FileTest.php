<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\UploadingFile;
use App\Database\UploadingFileChunk;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private function createFile(bool $execute = true, string $name = 'Testfile'): File
    {
        $file = new File();
        $file->path = 'this-does-not-exist';
        $file->name = $name;
        $file->type = 'application/octet-stream';
        if ($execute) {
            $file->create();
        }

        return $file;
    }

    public function testFindByKeyword(): void
    {
        $fileToFind = $this->createFile();
        $this->createFile(name: 'File');

        $files = iterator_to_array(File::findByKeyword('Test'));
        $this->assertCount(1, $files);
        $this->assertEquals($fileToFind->name, $files[0]->name);
    }

    public function testGetUploadChunksShouldGetTwo(): void
    {
        $file = $this->createFile();
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $file->getIdAsInt();
        $uploadingFile->create();
        $uploadFileChunk = new UploadingFileChunk();
        $uploadFileChunk->chunkPath = 'not-existent';
        $uploadFileChunk->chunkPosition = 0;
        $uploadFileChunk->uploadingFileId = $uploadingFile->getIdAsString();
        $uploadFileChunk->create();

        $uploadFileChunk2 = new UploadingFileChunk();
        $uploadFileChunk2->chunkPath = 'not-existent';
        $uploadFileChunk2->chunkPosition = 1;
        $uploadFileChunk2->uploadingFileId = $uploadingFile->getIdAsString();
        $uploadFileChunk2->create();

        $chunks = $file->getUploadChunks();
        $this->assertCount(2, $chunks);
    }

    public function testGetUploadChunksShouldGetNone(): void
    {
        $file = $this->createFile();
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $file->getIdAsInt();
        $uploadingFile->create();
        $uploadFileChunk = new UploadingFileChunk();
        $uploadFileChunk->chunkPath = 'not-existent';
        $uploadFileChunk->chunkPosition = 0;
        $uploadFileChunk->uploadingFileId = $uploadingFile->getIdAsString();
        $uploadFileChunk->create();

        $uploadFileChunk2 = new UploadingFileChunk();
        $uploadFileChunk2->chunkPath = 'not-existent';
        $uploadFileChunk2->chunkPosition = 1;
        $uploadFileChunk2->uploadingFileId = $uploadingFile->getIdAsString();
        $uploadFileChunk2->create();

        $file2 = $this->createFile(name: 'Test2');
        $chunks = $file2->getUploadChunks();
        $this->assertCount(0, $chunks);
    }

    public function testFormat(): void
    {
        $file = $this->createFile();
        $formattedFile = $file->format();
        $this->assertArrayHasKey('id', $formattedFile);
        $this->assertArrayHasKey('name', $formattedFile);
        $this->assertArrayHasKey('type', $formattedFile);
        $this->assertArrayHasKey('path', $formattedFile);
        $this->assertArrayHasKey('created', $formattedFile);
    }

    public function testUpdate(): void
    {
        $file = $this->createFile();
        $this->assertEquals('Testfile', $file->name);

        $file->name = 'Updated file';
        $file->update();

        $updatedFile = File::findById($file->getIdAsInt());
        $this->assertEquals($file->name, $updatedFile->name);
    }

    public function testUpdateUniqueFailed(): void
    {
        $this->expectException(UniqueFailedException::class);
        $this->createFile();
        $file2 = $this->createFile(name: 'Some other file');

        $file2->name = 'Testfile';
        $file2->update();
    }

    public function testFindById(): void
    {
        $file = $this->createFile();
        $foundFile = File::findById($file->getIdAsInt());

        $this->assertEquals($file->id, $foundFile->id);
        $this->assertEquals($file->name, $foundFile->name);
    }

    public function testFindByIdNotFound(): void
    {
        $foundFile = File::findById(-1);
        $this->assertNull($foundFile);
    }

    public function testFindAll(): void
    {
        $this->createFile();
        $this->createFile(name: 'Testfile2');
        $this->createFile(name: 'Testfile3');

        $files = File::findAll();
        $this->assertCount(3, $files);
    }

    public function testFindAllNoneFound(): void
    {
        $files = File::findAll();
        $this->assertCount(0, $files);
    }

    public function testGetCreator(): void
    {
        $file = $this->createFile();
        $creator = $file->getCreator();

        $this->assertNotNull($creator);
        $this->assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testDelete(): void
    {
        $file = $this->createFile();
        $file->delete();

        $foundFile = File::findById($file->getIdAsInt());
        $this->assertNull($foundFile);
    }

    public function testDeleteNotFound(): void
    {
        $file = $this->createFile(execute: false);
        $file->delete();

        $foundFile = File::findById($file->getIdAsInt());
        $this->assertNull($foundFile);
    }

    public function testCreate(): void
    {
        $file = $this->createFile(execute: false);
        $file->create();

        $foundFile = File::findById($file->getIdAsInt());

        $this->assertEquals($file->id, $foundFile->id);
        $this->assertEquals($file->name, $foundFile->name);
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(UniqueFailedException::class);
        $file = $this->createFile(execute: false);
        $file->create();
        $file = $this->createFile(execute: false);
        $file->create();
    }

    public function testGetUpdatedBy(): void
    {
        $file = $this->createFile();
        $updatedBy = $file->getUpdatedBy();

        $this->assertNotNull($updatedBy);
        $this->assertEquals(CurrentUser::$currentUser, $updatedBy);
    }
}
