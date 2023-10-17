<?php

namespace Jinya\Tests\Database;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\FileTag;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\UuidGenerator;

class FileTagTest extends DatabaseAwareTestCase
{

    public function testFindById(): void
    {
        $tag = $this->createTag();

        $foundTag = FileTag::findById($tag->getIdAsInt());

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
        $tag = $this->createTag(false);
        $formattedTag = $tag->format();

        self::assertArrayHasKey('color', $formattedTag);
        self::assertArrayHasKey('emoji', $formattedTag);
        self::assertArrayHasKey('name', $formattedTag);
    }

    public function testFindByKeyword(): void
    {
        $this->createTag();
        $tag = $this->createTag();

        $tags = iterator_to_array(FileTag::findByKeyword($tag->name));

        self::assertCount(1, $tags);
        self::assertEquals($tag->format(), $tags[0]->format());
    }

    public function testFindByKeywordNoResult(): void
    {
        $this->createTag();
        $this->createTag();

        $tags = iterator_to_array(FileTag::findByKeyword('Non existent'));

        self::assertEmpty($tags);
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
        $this->expectException(UniqueFailedException::class);
        $tag1 = $this->createTag();
        $tag2 = $this->createTag();
        $tag2->name = $tag1->name;
        $tag2->update();
    }

    public function testUpdateNonExistent(): void
    {
        $tag = $this->createTag(false);
        $tag->update();

        self::assertTrue(true);
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

        $createdTag = FileTag::findById($tag->getIdAsInt());
        self::assertNotNull($createdTag);
        self::assertEquals($tag->format(), $createdTag->format());
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(UniqueFailedException::class);
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

        self::assertNull(FileTag::findById($tag->getIdAsInt()));
    }

    public function testDeleteNonExistent(): void
    {
        $tag = $this->createTag(false);
        $tag->delete();

        self::assertTrue(true);
    }
}
