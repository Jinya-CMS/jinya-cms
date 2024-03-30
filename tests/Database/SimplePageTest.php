<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Tests\DatabaseAwareTestCase;

class SimplePageTest extends DatabaseAwareTestCase
{
    public function testGetCreator(): void
    {
        $page = $this->createPage();
        self::assertEquals(CurrentUser::$currentUser->format(), $page->getCreator()->format());
    }

    private function createPage(string $title = 'Test', bool $execute = true): ClassicPage
    {
        $page = new ClassicPage();
        $page->title = $title;
        $page->content = 'Test';
        if ($execute) {
            $page->create();
        }

        return $page;
    }

    public function testFormat(): void
    {
        $page = $this->createPage();
        self::assertEquals([
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
        $all = ClassicPage::findAll();
        self::assertNotEmpty(iterator_to_array($all));

        $page->delete();
        $all = ClassicPage::findAll();
        self::assertCount(0, iterator_to_array($all));
    }

    public function testFindById(): void
    {
        $page = $this->createPage();
        $foundPage = ClassicPage::findById($page->id);

        self::assertEquals($page->format(), $foundPage->format());
    }

    public function testFindByIdNonExistent(): void
    {
        $foundPage = ClassicPage::findById(-1);
        self::assertNull($foundPage);
    }

    public function testFindAll(): void
    {
        $this->createPage('Test 1');
        $this->createPage('Test 2');
        $this->createPage('Test 3');
        $this->createPage('Test 4');

        $all = ClassicPage::findAll();
        self::assertCount(4, iterator_to_array($all));
    }

    public function testCreate(): void
    {
        $page = $this->createPage(execute: false);
        $page->create();

        $foundPage = ClassicPage::findById($page->id);
        self::assertEquals($page->format(), $foundPage->format());
    }

    public function testGetUpdatedBy(): void
    {
        $page = $this->createPage();
        self::assertEquals(CurrentUser::$currentUser->format(), $page->getUpdatedBy()->format());
    }
}
