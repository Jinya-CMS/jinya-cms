<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\File;
use App\Database\Gallery;
use App\Database\ModernPage;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use PDOException;

class SectionPageTest extends DatabaseAwareTestCase
{
    public function testCreate(): void
    {
        $page = $this->createSectionPage(false);
        $page->create();

        $foundPage = ModernPage::findById($page->id);
        self::assertEquals($page->format(), $foundPage->format());
    }

    private function createSectionPage(bool $execute = true, string $name = 'Test'): ModernPage
    {
        $page = new ModernPage();
        $page->name = $name;

        if ($execute) {
            $page->create();
        }

        return $page;
    }

    public function testCreateDuplicate(): void
    {
        $this->expectException(PDOException::class);
        $this->createSectionPage();
        $this->createSectionPage();
    }

    public function testDelete(): void
    {
        $page = $this->createSectionPage();
        $page->delete();

        self::assertNull(ModernPage::findById($page->id));
    }

    public function testDeleteNotExistent(): void
    {
        $this->expectError();
        $page = $this->createSectionPage(false);
        $page->delete();
    }

    public function testFormat(): void
    {
        $page = $this->createSectionPage();
        self::assertEquals([
            'id' => $page->id,
            'name' => $page->name,
            'sectionCount' => 0,
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
        $this->createSectionPage(name: 'Test 1');
        $this->createSectionPage(name: 'Test 2');
        $this->createSectionPage(name: 'Test 3');
        $this->createSectionPage(name: 'Test 4');
        $found = ModernPage::findAll();
        self::assertCount(4, iterator_to_array($found));
    }

    public function testFindAllNoneCreated(): void
    {
        $found = ModernPage::findAll();
        self::assertCount(0, iterator_to_array($found));
    }

    public function testFindById(): void
    {
        $page = $this->createSectionPage();
        $foundPage = ModernPage::findById($page->id);

        self::assertEquals($page->format(), $foundPage->format());
    }

    public function testFindByIdNotExistent(): void
    {
        $this->createSectionPage();
        $foundPage = ModernPage::findById(-100);

        self::assertNull($foundPage);
    }

    public function testUpdate(): void
    {
        $page = $this->createSectionPage();
        $page->name = 'Start';
        $page->update();

        $foundPage = ModernPage::findById($page->id);
        self::assertEquals($page->format(), $foundPage->format());
    }

    public function testGetSectionsNoSections(): void
    {
        $page = $this->createSectionPage();

        $sections = $page->getSections();
        self::assertCount(0, iterator_to_array($sections));
    }

    public function testGetCreator(): void
    {
        $page = $this->createSectionPage();
        $creator = $page->getCreator();
        self::assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testReplaceSectionsEmptyArray(): void
    {
        $page = $this->createSectionPage();
        $page->replaceSections([
            ['html' => 'Test Section'],
        ]);

        self::assertCount(1, iterator_to_array($page->getSections()));

        $page->replaceSections([]);
        self::assertCount(0, iterator_to_array($page->getSections()));
    }

    public function testReplaceSectionsCreateSections(): void
    {
        $page = $this->createSectionPage();
        $page->replaceSections([
            ['html' => 'Test Section'],
        ]);

        self::assertCount(1, iterator_to_array($page->getSections()));

        $file = $this->createFile();
        $gallery = $this->createGallery();
        $page->replaceSections([
            ['html' => 'Test Section'],
            ['file' => $file->id],
            ['file' => $file->id, 'link' => 'https://google.com'],
            ['file' => $file->id, 'script' => 'https://google.com'],
            ['gallery' => $gallery->id],
        ]);

        $sections = $page->getSections();
        self::assertCount(5, iterator_to_array($sections));

        $sections = $page->getSections();

        $Section = $sections->current();
        self::assertEquals('Test Section', $Section->html);
        self::assertEquals(0, $Section->position);
        $sections->next();

        $Section = $sections->current();
        self::assertEquals($file->id, $Section->fileId);
        self::assertEquals(1, $Section->position);
        self::assertEquals('none', $Section->action);
        $sections->next();

        $Section = $sections->current();
        self::assertEquals($file->id, $Section->fileId);
        self::assertEquals('https://google.com', $Section->target);
        self::assertEquals('link', $Section->action);
        self::assertEquals(2, $Section->position);
        $sections->next();

        $Section = $sections->current();
        self::assertEquals($file->id, $Section->fileId);
        self::assertEquals('https://google.com', $Section->script);
        self::assertEquals('script', $Section->action);
        self::assertEquals(3, $Section->position);
        $sections->next();

        $Section = $sections->current();
        self::assertEquals($gallery->id, $Section->galleryId);
        self::assertEquals(4, $Section->position);
        $sections->next();
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
}
