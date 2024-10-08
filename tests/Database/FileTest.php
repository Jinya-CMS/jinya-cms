<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Utils\UuidGenerator;
use PDOException;

class FileTest extends DatabaseAwareTestCase
{
    public function testGetUploadChunksShouldGetTwo(): void
    {
        $file = $this->createFile();
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $file->id;
        $uploadingFile->create();
        $uploadFileChunk = new UploadingFileChunk();
        $uploadFileChunk->chunkPath = 'not-existent';
        $uploadFileChunk->chunkPosition = 0;
        $uploadFileChunk->uploadingFileId = $uploadingFile->id;
        $uploadFileChunk->create();

        $uploadFileChunk2 = new UploadingFileChunk();
        $uploadFileChunk2->chunkPath = 'not-existent';
        $uploadFileChunk2->chunkPosition = 1;
        $uploadFileChunk2->uploadingFileId = $uploadingFile->id;
        $uploadFileChunk2->create();

        $chunks = $file->getUploadChunks();
        self::assertCount(2, iterator_to_array($chunks));
    }

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

    public function testGetUploadChunksShouldGetNone(): void
    {
        $file = $this->createFile();
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $file->id;
        $uploadingFile->create();
        $uploadFileChunk = new UploadingFileChunk();
        $uploadFileChunk->chunkPath = 'not-existent';
        $uploadFileChunk->chunkPosition = 0;
        $uploadFileChunk->uploadingFileId = $uploadingFile->id;
        $uploadFileChunk->create();

        $uploadFileChunk2 = new UploadingFileChunk();
        $uploadFileChunk2->chunkPath = 'not-existent';
        $uploadFileChunk2->chunkPosition = 1;
        $uploadFileChunk2->uploadingFileId = $uploadingFile->id;
        $uploadFileChunk2->create();

        $file2 = $this->createFile(name: 'Test2');
        $chunks = $file2->getUploadChunks();
        self::assertCount(0, iterator_to_array($chunks));
    }

    public function testFormat(): void
    {
        $file = $this->createFile();
        $formattedFile = $file->format();
        self::assertArrayHasKey('id', $formattedFile);
        self::assertArrayHasKey('name', $formattedFile);
        self::assertArrayHasKey('type', $formattedFile);
        self::assertArrayHasKey('path', $formattedFile);
        self::assertArrayHasKey('created', $formattedFile);
        self::assertArrayHasKey('tags', $formattedFile);
    }

    public function testFormatWithTags(): void
    {
        $tag = new FileTag();
        $tag->name = 'Testtag';
        $tag->create();

        $file = $this->createFile();
        $file->tags = [$tag->name];
        $file->update();

        $formattedFile = $file->format();
        self::assertArrayHasKey('id', $formattedFile);
        self::assertArrayHasKey('name', $formattedFile);
        self::assertArrayHasKey('type', $formattedFile);
        self::assertArrayHasKey('path', $formattedFile);
        self::assertArrayHasKey('created', $formattedFile);
        self::assertArrayHasKey('tags', $formattedFile);

        self::assertEquals($tag->format(), $formattedFile['tags'][0]);
    }

    public function testUpdate(): void
    {
        $file = $this->createFile();
        self::assertEquals('Testfile', $file->name);

        $file->name = 'Updated file';
        $file->update();

        $updatedFile = File::findById($file->id);
        self::assertEquals($file->name, $updatedFile->name);
    }

    public function testUpdateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $this->createFile();
        $file2 = $this->createFile(name: 'Some other file');

        $file2->name = 'Testfile';
        $file2->update();
    }

    public function testFindById(): void
    {
        $file = $this->createFile();
        $foundFile = File::findById($file->id);

        self::assertEquals($file->id, $foundFile->id);
        self::assertEquals($file->name, $foundFile->name);
    }

    public function testFindByIdNotFound(): void
    {
        $foundFile = File::findById(-1);
        self::assertNull($foundFile);
    }

    public function testFindAll(): void
    {
        $this->createFile();
        $this->createFile(name: 'Testfile2');
        $this->createFile(name: 'Testfile3');

        $files = File::findAll();
        self::assertCount(3, iterator_to_array($files));
    }

    public function testFindAllNoneFound(): void
    {
        $files = File::findAll();
        self::assertCount(0, iterator_to_array($files));
    }

    public function testFindRoot(): void
    {
        $this->createFile();
        $this->createFile(name: 'Testfile2');
        $file = $this->createFile(name: 'Testfile3');

        $folder = new Folder();
        $folder->name = UuidGenerator::generateV4();
        $folder->create();

        $file->folderId = $folder->id;
        $file->update();

        $files = File::findRootFiles();
        self::assertCount(2, iterator_to_array($files));
    }

    public function testGetCreator(): void
    {
        $file = $this->createFile();
        $creator = $file->getCreator();

        self::assertNotNull($creator);
        self::assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testDelete(): void
    {
        $file = $this->createFile();
        $file->delete();

        $foundFile = File::findById($file->id);
        self::assertNull($foundFile);
    }

    public function testDeleteNotFound(): void
    {
        $this->expectError();
        $file = $this->createFile(execute: false);
        $file->delete();
    }

    public function testCreate(): void
    {
        $file = $this->createFile(execute: false);
        $file->create();

        $foundFile = File::findById($file->id);

        self::assertEquals($file->id, $foundFile->id);
        self::assertEquals($file->name, $foundFile->name);
    }

    public function testCreateWithTags(): void
    {
        $tag = new FileTag();
        $tag->name = 'Test';
        $tag->create();

        $file = $this->createFile(execute: false);
        $file->tags = [$tag->name, 'Not existing'];
        $file->create();

        $foundFile = File::findById($file->id);

        self::assertEquals($file->id, $foundFile->id);
        self::assertEquals($file->name, $foundFile->name);

        $tags = iterator_to_array($file->getTags());
        self::assertNotEmpty($tags);
        self::assertCount(1, $tags);
        self::assertEquals($tag->name, $tags[0]->name);
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $file = $this->createFile(execute: false);
        $file->create();
        $file = $this->createFile(execute: false);
        $file->create();
    }

    public function testGetUpdatedBy(): void
    {
        $file = $this->createFile();
        $updatedBy = $file->getUpdatedBy();

        self::assertNotNull($updatedBy);
        self::assertEquals(CurrentUser::$currentUser, $updatedBy);
    }
}
