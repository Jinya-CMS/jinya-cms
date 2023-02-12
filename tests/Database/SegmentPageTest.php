<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;

class SegmentPageTest extends DatabaseAwareTestCase
{

    public function testCreate(): void
    {
        $page = $this->createSegmentPage(false);
        $page->create();

        $foundPost = SegmentPage::findById($page->getIdAsInt());
        $this->assertEquals($page->format(), $foundPost->format());
    }

    private function createSegmentPage(bool $execute = true, string $title = 'Test'): SegmentPage
    {
        $page = new SegmentPage();
        $page->name = $title;

        if ($execute) {
            $page->create();
        }

        return $page;
    }

    public function testCreateDuplicate(): void
    {
        $this->expectException(UniqueFailedException::class);
        $this->createSegmentPage();
        $this->createSegmentPage();
    }

    public function testDelete(): void
    {
        $page = $this->createSegmentPage();
        $page->delete();

        $this->assertNull(SegmentPage::findById($page->getIdAsInt()));
    }

    public function testDeleteNotExistent(): void
    {
        $page = $this->createSegmentPage(false);
        $page->delete();
        $this->assertTrue(true);
    }

    public function testFormat(): void
    {
        $page = $this->createSegmentPage();
        $this->assertEquals([
            'id' => $page->id,
            'name' => $page->name,
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
            'segmentCount' => 0,
        ], $page->format());
    }

    public function testFindAll(): void
    {
        $this->createSegmentPage(title: 'Test 1');
        $this->createSegmentPage(title: 'Test 2');
        $this->createSegmentPage(title: 'Test 3');
        $this->createSegmentPage(title: 'Test 4');
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
        $foundPost = SegmentPage::findById($page->getIdAsInt());

        $this->assertEquals($page->format(), $foundPost->format());
    }

    public function testFindByIdNotExistent(): void
    {
        $this->createSegmentPage();
        $foundPost = SegmentPage::findById(-100);

        $this->assertNull($foundPost);
    }

    public function testUpdate(): void
    {
        $page = $this->createSegmentPage();
        $page->name = 'Start';
        $page->update();

        $foundPost = SegmentPage::findById($page->getIdAsInt());
        $this->assertEquals($page->format(), $foundPost->format());
    }

    public function testFindByKeyword(): void
    {
        $this->createSegmentPage(title: 'Test 1');
        $this->createSegmentPage(title: 'Test 2');
        $this->createSegmentPage(title: 'Test 3');
        $this->createSegmentPage(title: 'Test 4');
        $found = SegmentPage::findByKeyword('test');
        $this->assertCount(4, iterator_to_array($found));

        $found = SegmentPage::findByKeyword('Test 1');
        $this->assertCount(1, iterator_to_array($found));

        $found = SegmentPage::findByKeyword('Test 15');
        $this->assertCount(0, iterator_to_array($found));
    }

    public function testGetSegments(): void
    {
        $page = $this->createSegmentPage();
        $this->createSegment($page->getIdAsInt());
        $this->createSegment($page->getIdAsInt());
        $this->createSegment($page->getIdAsInt());
        $this->createSegment($page->getIdAsInt());

        $segments = $page->getSegments();
        $this->assertCount(4, iterator_to_array($segments));
    }

    private function createSegment(int $segmentPageId, int $position = 0): Segment
    {
        $segment = new Segment();
        $segment->pageId = $segmentPageId;
        $segment->position = $position;
        $segment->create();

        return $segment;
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

    public function testCreateSegments(): void
    {
        $page = $this->createSegmentPage();
        $htmlSegment = $this->createSegment($page->getIdAsInt(), 0);
        $fileSegment = $this->createSegment($page->getIdAsInt(), 1);
        $fileWithLinkSegment = $this->createSegment($page->getIdAsInt(), 2);
        $gallerySegment = $this->createSegment($page->getIdAsInt(), 3);
        $formSegment = $this->createSegment($page->getIdAsInt(), 4);

        $this->assertCount(5, iterator_to_array($page->getSegments()));

        $file = $this->createFile();
        $gallery = $this->createGallery();
        $form = $this->createForm();

        $htmlSegment->html = 'Test segment';
        $htmlSegment->update();

        $fileSegment->fileId = $file->getIdAsInt();
        $fileSegment->update();

        $fileWithLinkSegment->fileId = $file->getIdAsInt();
        $fileWithLinkSegment->target = 'https://google.com';
        $fileWithLinkSegment->action = 'link';
        $fileWithLinkSegment->update();

        $gallerySegment->galleryId = $gallery->getIdAsInt();
        $gallerySegment->update();

        $formSegment->formId = $form->getIdAsInt();
        $formSegment->update();

        $segments = $page->getSegments();

        $segment = $segments->current();
        $this->assertEquals('Test segment', $segment->html);
        $this->assertEquals(0, $segment->position);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($file->getIdAsInt(), $segment->fileId);
        $this->assertEquals(1, $segment->position);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($file->getIdAsInt(), $segment->fileId);
        $this->assertEquals('link', $segment->action);
        $this->assertEquals('https://google.com', $segment->target);
        $this->assertEquals(2, $segment->position);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($gallery->getIdAsInt(), $segment->galleryId);
        $this->assertEquals(3, $segment->position);
        $segments->next();
    }

    private function createFile(): File
    {
        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        return $file;
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = 'Gallery';
        $gallery->create();

        return $gallery;
    }

    private function createForm(): Form
    {
        $form = new Form();
        $form->title = 'Form';
        $form->toAddress = 'test@example.com';
        $form->create();

        return $form;
    }
}
