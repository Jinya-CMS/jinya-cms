<?php

namespace Jinya\Tests\Database;

use App\Database\File;
use App\Database\FileTag;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\UuidGenerator;
use PDOException;

class FileTagTest extends DatabaseAwareTestCase
{
    public function testFindById(): void
    {
        $tag = $this->createTag();

        $foundTag = FileTag::findById($tag->id);

        self::assertEquals($tag->format(), $foundTag->format());
    }

    private function createTag(bool $execute = true): FileTag
    {
        $tag = new FileTag();
        $tag->name = UuidGenerator::generateV4();
        $tag->emoji = '4';
        $tag->color = '#123456';
        if ($execute) {
            $tag->create();
        }

        return $tag;
    }

    public function testFindByIdNotExistent(): void
    {
        $foundTag = FileTag::findById(-1);

        self::assertNull($foundTag);
    }

    public function testFindByName(): void
    {
        $tag = $this->createTag();

        $foundTag = FileTag::findByName($tag->name);

        self::assertEquals($tag->format(), $foundTag->format());
    }

    public function testFindByNameNotExistent(): void
    {
        $foundTag = FileTag::findByName('Not found');

        self::assertNull($foundTag);
    }

    public function testFormat(): void
    {
        $tag = $this->createTag();
        $formattedTag = $tag->format();

        self::assertArrayHasKey('color', $formattedTag);
        self::assertArrayHasKey('emoji', $formattedTag);
        self::assertArrayHasKey('name', $formattedTag);
    }

    public function testUpdate(): void
    {
        $tag = $this->createTag();
        $tag->name = 'Test';
        $tag->update();

        $updatedTag = FileTag::findByName('Test');
        self::assertNotNull($updatedTag);
    }

    public function testUpdateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $tag1 = $this->createTag();
        $tag2 = $this->createTag();
        $tag2->name = $tag1->name;
        $tag2->update();
    }

    public function testUpdateNonExistent(): void
    {
        $this->expectError();
        $tag = $this->createTag(false);
        $tag->update();
    }

    public function testGetFiles(): void
    {
        $tag = $this->createTag();

        $file = new File();
        $file->tags = [$tag->name];
        $file->name = 'Test';
        $file->create();

        $files = iterator_to_array($tag->getFiles());
        self::assertNotEmpty($files);
        self::assertEquals($file->format(), $files[0]->format());
    }

    public function testCreate(): void
    {
        $tag = $this->createTag(false);
        $tag->create();

        $createdTag = FileTag::findById($tag->id);
        self::assertNotNull($createdTag);
        self::assertEquals($tag->format(), $createdTag->format());
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $tag1 = $this->createTag();
        $tag = $this->createTag(false);
        $tag->name = $tag1->name;
        $tag->create();
    }

    public function testFindAll(): void
    {
        $this->createTag();
        $this->createTag();

        $tags = iterator_to_array(FileTag::findAll());

        self::assertCount(2, $tags);
    }

    public function testDelete(): void
    {
        $tag = $this->createTag();
        $tag->delete();

        self::assertNull(FileTag::findById($tag->id));
    }

    public function testDeleteNonExistent(): void
    {
        $this->expectError();
        $tag = $this->createTag(false);
        $tag->delete();
    }
}
