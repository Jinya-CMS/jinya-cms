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
        $this->assertEquals($page->format(), $foundPage->format());
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
        $this->createSectionPage();
        $this->createSectionPage();
    }

    public function testDelete(): void
    {
        $page = $this->createSectionPage();
        $page->delete();

        $this->assertNull(ModernPage::findById($page->id));
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
        $this->assertEquals([
            'id' => $page->id,
            'name' => $page->name,
            'SectionCount' => 0,
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
        $this->assertCount(4, iterator_to_array($found));
    }

    public function testFindAllNoneCreated(): void
    {
        $found = ModernPage::findAll();
        $this->assertCount(0, iterator_to_array($found));
    }

    public function testFindById(): void
    {
        $page = $this->createSectionPage();
        $foundPage = ModernPage::findById($page->id);

        $this->assertEquals($page->format(), $foundPage->format());
    }

    public function testFindByIdNotExistent(): void
    {
        $this->createSectionPage();
        $foundPage = ModernPage::findById(-100);

        $this->assertNull($foundPage);
    }

    public function testUpdate(): void
    {
        $page = $this->createSectionPage();
        $page->name = 'Start';
        $page->update();

        $foundPage = ModernPage::findById($page->id);
        $this->assertEquals($page->format(), $foundPage->format());
    }

    public function testGetSectionsNoSections(): void
    {
        $page = $this->createSectionPage();

        $sections = $page->getSections();
        $this->assertCount(0, iterator_to_array($sections));
    }

    public function testGetCreator(): void
    {
        $page = $this->createSectionPage();
        $creator = $page->getCreator();
        $this->assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testReplaceSectionsEmptyArray(): void
    {
        $page = $this->createSectionPage();
        $page->replaceSections([
            ['html' => 'Test Section'],
        ]);

        $this->assertCount(1, iterator_to_array($page->getSections()));

        $page->replaceSections([]);
        $this->assertCount(0, iterator_to_array($page->getSections()));
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = 'Gallery';
        $gallery->create();

        return $gallery;
    }

    public function testReplaceSectionsCreateSections(): void
    {
        $page = $this->createSectionPage();
        $page->replaceSections([
            ['html' => 'Test Section'],
        ]);

        $this->assertCount(1, iterator_to_array($page->getSections()));

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
        $this->assertCount(5, iterator_to_array($sections));

        $sections = $page->getSections();

        $Section = $sections->current();
        $this->assertEquals('Test Section', $Section->html);
        $this->assertEquals(0, $Section->position);
        $sections->next();

        $Section = $sections->current();
        $this->assertEquals($file->id, $Section->fileId);
        $this->assertEquals(1, $Section->position);
        $this->assertEquals('none', $Section->action);
        $sections->next();

        $Section = $sections->current();
        $this->assertEquals($file->id, $Section->fileId);
        $this->assertEquals('https://google.com', $Section->target);
        $this->assertEquals('link', $Section->action);
        $this->assertEquals(2, $Section->position);
        $sections->next();

        $Section = $sections->current();
        $this->assertEquals($file->id, $Section->fileId);
        $this->assertEquals('https://google.com', $Section->script);
        $this->assertEquals('script', $Section->action);
        $this->assertEquals(3, $Section->position);
        $sections->next();

        $Section = $sections->current();
        $this->assertEquals($gallery->id, $Section->galleryId);
        $this->assertEquals(4, $Section->position);
        $sections->next();
    }
}
