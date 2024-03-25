<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\File;
use App\Database\Gallery;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use PDOException;

class SegmentPageTest extends DatabaseAwareTestCase
{
    public function testCreate(): void
    {
        $page = $this->createSegmentPage(false);
        $page->create();

        $foundPage = SegmentPage::findById($page->id);
        $this->assertEquals($page->format(), $foundPage->format());
    }

    private function createSegmentPage(bool $execute = true, string $name = 'Test'): SegmentPage
    {
        $page = new SegmentPage();
        $page->name = $name;

        if ($execute) {
            $page->create();
        }

        return $page;
    }

    private function createFile(): File
    {
        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        return $file;
    }

    public function testCreateDuplicate(): void
    {
        $this->expectException(PDOException::class);
        $this->createSegmentPage();
        $this->createSegmentPage();
    }

    public function testDelete(): void
    {
        $page = $this->createSegmentPage();
        $page->delete();

        $this->assertNull(SegmentPage::findById($page->id));
    }

    public function testDeleteNotExistent(): void
    {
        $this->expectError();
        $page = $this->createSegmentPage(false);
        $page->delete();
    }

    public function testFormat(): void
    {
        $page = $this->createSegmentPage();
        $this->assertEquals([
            'id' => $page->id,
            'name' => $page->name,
            'segmentCount' => 0,
            'created' => [
                'at' => $page->createdAt->format(DATE_ATOM),
                'by' => [
                    'artistName' => CurrentUser::$currentUser->artistName,
                    'email' => CurrentUser::$currentUser->email,
                    'profilePicture' => CurrentUser::$currentUser->profilePicture,
                ],
            ],
            'updated' => [
                'at' => $page->lastUpdatedAt->format(DATE_ATOM),
                'by' => [
                    'artistName' => CurrentUser::$currentUser->artistName,
                    'email' => CurrentUser::$currentUser->email,
                    'profilePicture' => CurrentUser::$currentUser->profilePicture,
                ],
            ],
        ], $page->format());
    }


    public function testFindAll(): void
    {
        $this->createSegmentPage(name: 'Test 1');
        $this->createSegmentPage(name: 'Test 2');
        $this->createSegmentPage(name: 'Test 3');
        $this->createSegmentPage(name: 'Test 4');
        $found = SegmentPage::findAll();
        $this->assertCount(4, iterator_to_array($found));
    }

    public function testFindAllNoneCreated(): void
    {
        $found = SegmentPage::findAll();
        $this->assertCount(0, iterator_to_array($found));
    }

    public function testFindById(): void
    {
        $page = $this->createSegmentPage();
        $foundPage = SegmentPage::findById($page->id);

        $this->assertEquals($page->format(), $foundPage->format());
    }

    public function testFindByIdNotExistent(): void
    {
        $this->createSegmentPage();
        $foundPage = SegmentPage::findById(-100);

        $this->assertNull($foundPage);
    }

    public function testUpdate(): void
    {
        $page = $this->createSegmentPage();
        $page->name = 'Start';
        $page->update();

        $foundPage = SegmentPage::findById($page->id);
        $this->assertEquals($page->format(), $foundPage->format());
    }

    public function testGetSegmentsNoSegments(): void
    {
        $page = $this->createSegmentPage();

        $segments = $page->getSegments();
        $this->assertCount(0, iterator_to_array($segments));
    }

    public function testGetCreator(): void
    {
        $page = $this->createSegmentPage();
        $creator = $page->getCreator();
        $this->assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testReplaceSegmentsEmptyArray(): void
    {
        $page = $this->createSegmentPage();
        $page->replaceSegments([
            ['html' => 'Test segment'],
        ]);

        $this->assertCount(1, iterator_to_array($page->getSegments()));

        $page->replaceSegments([]);
        $this->assertCount(0, iterator_to_array($page->getSegments()));
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = 'Gallery';
        $gallery->create();

        return $gallery;
    }

    public function testReplaceSegmentsCreateSegments(): void
    {
        $page = $this->createSegmentPage();
        $page->replaceSegments([
            ['html' => 'Test segment'],
        ]);

        $this->assertCount(1, iterator_to_array($page->getSegments()));

        $file = $this->createFile();
        $gallery = $this->createGallery();
        $page->replaceSegments([
            ['html' => 'Test segment'],
            ['file' => $file->id],
            ['file' => $file->id, 'link' => 'https://google.com'],
            ['file' => $file->id, 'script' => 'https://google.com'],
            ['gallery' => $gallery->id],
        ]);

        $segments = $page->getSegments();
        $this->assertCount(5, iterator_to_array($segments));

        $segments = $page->getSegments();

        $segment = $segments->current();
        $this->assertEquals('Test segment', $segment->html);
        $this->assertEquals(0, $segment->position);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($file->id, $segment->fileId);
        $this->assertEquals(1, $segment->position);
        $this->assertEquals('none', $segment->action);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($file->id, $segment->fileId);
        $this->assertEquals('https://google.com', $segment->target);
        $this->assertEquals('link', $segment->action);
        $this->assertEquals(2, $segment->position);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($file->id, $segment->fileId);
        $this->assertEquals('https://google.com', $segment->script);
        $this->assertEquals('script', $segment->action);
        $this->assertEquals(3, $segment->position);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($gallery->id, $segment->galleryId);
        $this->assertEquals(4, $segment->position);
        $segments->next();
    }
}
