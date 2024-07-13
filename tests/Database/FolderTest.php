<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Utils\UuidGenerator;

class FolderTest extends DatabaseAwareTestCase
{

    private function createFolder(string $name = 'folder1', ?int $parent = null): Folder
    {
        $folder = new Folder();
        $folder->name = $name;
        $folder->parentId = $parent;
        $folder->create();

        return $folder;
    }

    public function testFindRoot(): void
    {
        $this->createFolder();
        $root = $this->createFolder(name: 'Testfolder2');
        $this->createFolder(name: 'Testfolder3', parent: $root->id);

        $folders = Folder::findRootFolders();
        self::assertCount(2, iterator_to_array($folders));
    }

    public function testGetFiles(): void
    {
        $root = $this->createFolder();

        $file = new File();
        $file->name = UuidGenerator::generateV4();
        $file->create();

        $file = new File();
        $file->name = UuidGenerator::generateV4();
        $file->folderId = $root->id;
        $file->create();

        $files = iterator_to_array($root->getFiles());
        self::assertCount(1, $files);
        self::assertEquals($file->format(), $files[0]->format());
    }

    public function testJsonSerialize(): void
    {
        $root = $this->createFolder();

        $file = new File();
        $file->name = UuidGenerator::generateV4();
        $file->folderId = $root->id;
        $file->create();

        $folder = $this->createFolder(name: 'Testfolder3', parent: $root->id);

        $serialized = $root->jsonSerialize();
        self::assertArrayHasKey('name', $serialized);
        self::assertEquals($serialized['name'], $root->name);

        self::assertArrayHasKey('files', $serialized);
        self::assertCount(1, $serialized['files']);

        self::assertArrayHasKey('folders', $serialized);
        self::assertCount(1, $serialized['folders']);
    }

    public function testGetFolders(): void
    {
        $this->createFolder();
        $root = $this->createFolder(name: 'Testfolder2');
        $this->createFolder(name: 'Testfolder3', parent: $root->id);

        $folders = $root->getFolders();

        self::assertCount(1, iterator_to_array($folders));
    }

    public function testFormat(): void
    {
        $root = $this->createFolder();

        $file = new File();
        $file->name = UuidGenerator::generateV4();
        $file->folderId = $root->id;
        $file->create();

        $folder = $this->createFolder(name: 'Testfolder3', parent: $root->id);

        $serialized = $root->jsonSerialize();
        self::assertArrayHasKey('name', $serialized);
        self::assertEquals($serialized['name'], $root->name);

        self::assertArrayHasKey('files', $serialized);
        self::assertCount(1, $serialized['files']);

        self::assertArrayHasKey('folders', $serialized);
        self::assertCount(1, $serialized['folders']);
    }
}
