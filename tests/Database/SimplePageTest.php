<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\SimplePage;
use App\Tests\DatabaseAwareTestCase;

class SimplePageTest extends DatabaseAwareTestCase
{

    public function testGetCreator(): void
    {
        $page = $this->createPage();
        self::assertEquals(CurrentUser::$currentUser->format(), $page->getCreator()->format());
    }

    private function createPage(string $title = 'Test', string $content = 'Test', bool $execute = true): SimplePage
    {
        $page = new SimplePage();
        $page->title = $title;
        $page->content = $content;
        if ($execute) {
            $page->create();
        }

        return $page;
    }

    public function testFormat(): void
    {
        $page = $this->createPage();
        $this->assertEquals([
            'id' => $page->id,
            'title' => $page->title,
            'created' => [
                'at' => $page->createdAt->format(DATE_ATOM),
                'by' => [
                    'artistName' => CurrentUser::$currentUser->artistName,
                    'email' => CurrentUser::$currentUser->email,
                    'profilePicture' => CurrentUser::$currentUser->profilePicture,
                ],
            ],
            'content' => $page->content,
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

    public function testUpdate(): void
    {
        $page = $this->createPage();
        self::assertEquals('Test', $page->title);

        $page->title = 'Start';
        $page->update();

        self::assertEquals('Start', $page->title);
    }

    public function testDelete(): void
    {
        $page = $this->createPage();
        $all = SimplePage::findAll();
        self::assertNotEmpty(iterator_to_array($all));

        $page->delete();
        $all = SimplePage::findAll();
        self::assertCount(0, iterator_to_array($all));
    }

    public function testFindById(): void
    {
        $page = $this->createPage();
        $foundPage = SimplePage::findById($page->getIdAsInt());

        self::assertEquals($page->format(), $foundPage->format());
    }

    public function testFindByIdNonExistent(): void
    {
        $foundPage = SimplePage::findById(-1);
        self::assertNull($foundPage);
    }

    public function testFindAll(): void
    {
        $this->createPage('Test 1');
        $this->createPage('Test 2');
        $this->createPage('Test 3');
        $this->createPage('Test 4');

        $all = SimplePage::findAll();
        self::assertCount(4, iterator_to_array($all));
    }

    public function testCreate(): void
    {
        $page = $this->createPage(execute: false);
        $page->create();

        $foundPage = SimplePage::findById($page->getIdAsInt());
        self::assertEquals($page->format(), $foundPage->format());
    }

    public function testFindByKeyword(): void
    {
        $this->createPage('Test 1');
        $this->createPage('Test 2');
        $this->createPage('Test 3');
        $this->createPage('Test 4');

        $all = SimplePage::findByKeyword('Test');
        self::assertCount(4, iterator_to_array($all));

        $all = SimplePage::findByKeyword('Test 1');
        self::assertCount(1, iterator_to_array($all));

        $all = SimplePage::findByKeyword('Test 15');
        self::assertCount(0, iterator_to_array($all));
    }

    public function testGetUpdatedBy(): void
    {
        $page = $this->createPage();
        self::assertEquals(CurrentUser::$currentUser->format(), $page->getUpdatedBy()->format());
    }
}
