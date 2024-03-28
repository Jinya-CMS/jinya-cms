<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\File;
use App\Database\Gallery;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use PDOException;

class BlogPostTest extends DatabaseAwareTestCase
{
    public function testCreate(): void
    {
        $post = $this->createBlogPost(false);
        $post->create();

        $foundPost = BlogPost::findById($post->id);
        self::assertEquals($post->format(), $foundPost->format());
    }

    private function createBlogPost(
        bool $execute = true,
        bool $withHeaderImage = true,
        string $title = 'Test',
        string $slug = 'test'
    ): BlogPost {
        $post = new BlogPost();
        $post->title = $title;
        $post->slug = $slug;
        if ($withHeaderImage) {
            $post->headerImageId = $this->createFile()->id;
        }
        if (true) {
            $post->categoryId = $this->createBlogCategory()->id;
        }

        if ($execute) {
            $post->create();
        }

        return $post;
    }

    private function createFile(): File
    {
        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        return $file;
    }

    private function createBlogCategory(): BlogCategory
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        return $category;
    }

    public function testCreateWithWebhookHttp(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $post = $this->createBlogPost(false);
        $post->public = true;
        $category = $post->getCategory();
        $category->webhookEnabled = true;
        $category->webhookUrl = 'http://httpbin.org/post';
        $category->update();
        $post->create();

        $foundPost = BlogPost::findById($post->id);
        self::assertEquals($post->format(), $foundPost->format());
    }

    public function testCreateWithInvalidWebhook(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $post = $this->createBlogPost(false);
        $post->public = true;
        $category = $post->getCategory();
        $category->webhookEnabled = true;
        $category->webhookUrl = 'test';
        $category->update();
        $post->create();

        $foundPost = BlogPost::findById($post->id);
        self::assertEquals($post->format(), $foundPost->format());
    }

    public function testCreateWithWebhookHttps(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $post = $this->createBlogPost(false);
        $post->public = true;
        $category = $post->getCategory();
        $category->webhookEnabled = true;
        $category->webhookUrl = 'https://httpbin.org/post';
        $category->update();
        $post->create();

        $foundPost = BlogPost::findById($post->id);
        self::assertEquals($post->format(), $foundPost->format());
    }

    public function testUpdateWithWebhookHttps(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $post = $this->createBlogPost(false);
        $post->public = false;
        $category = $post->getCategory();
        $category->webhookEnabled = true;
        $category->webhookUrl = 'https://httpbin.org/post';
        $category->update();
        $post->create();

        $post->public = true;
        $post->update();
        self::assertTrue(true);
    }

    public function testCreateDuplicate(): void
    {
        $this->expectException(PDOException::class);
        $this->createBlogPost();
        $this->createBlogPost();
    }

    public function testGetHeaderImage(): void
    {
        $post = $this->createBlogPost();
        self::assertNotNull($post->getHeaderImage());
        self::assertEquals(File::findById($post->headerImageId), $post->getHeaderImage());
    }

    public function testGetHeaderImageEmpty(): void
    {
        $post = $this->createBlogPost(execute: false, withHeaderImage: false);
        self::assertNull($post->getHeaderImage());
    }

    public function testDelete(): void
    {
        $post = $this->createBlogPost();
        $post->delete();

        self::assertNull(BlogPost::findById($post->id));
    }

    public function testDeleteNotExistent(): void
    {
        $this->expectError();
        $post = $this->createBlogPost(false);
        $post->delete();
    }

    public function testFindBySlug(): void
    {
        $post = $this->createBlogPost();
        $foundPost = BlogPost::findBySlug($post->slug);

        self::assertEquals($post->format(), $foundPost->format());
    }

    public function testFindBySlugNotExistent(): void
    {
        $this->createBlogPost();
        $foundPost = BlogPost::findBySlug('start');

        self::assertNull($foundPost);
    }

    public function testFormat(): void
    {
        $post = $this->createBlogPost();
        self::assertEquals([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'headerImage' => [
                'id' => $post->getHeaderImage()->id,
                'name' => $post->getHeaderImage()->name,
                'path' => $post->getHeaderImage()->path,
            ],
            'category' => [
                'id' => $post->getCategory()->id,
                'name' => $post->getCategory()->name,
            ],
            'public' => $post->public,
            'created' => [
                'at' => $post->createdAt->format(DATE_ATOM),
                'by' => [
                    'artistName' => CurrentUser::$currentUser->artistName,
                    'email' => CurrentUser::$currentUser->email,
                    'profilePicture' => CurrentUser::$currentUser->profilePicture,
                ],
            ],
        ], $post->format());
    }

    public function testGetCategory(): void
    {
        $post = $this->createBlogPost();
        $category = BlogCategory::findById($post->categoryId);
        self::assertEquals($category, $post->getCategory());
    }

    public function testFindAll(): void
    {
        $this->createBlogPost(execute: 'Test 1', title: 'Test 1', slug: 'test-1');
        $this->createBlogPost(execute: 'Test 2', title: 'Test 2', slug: 'test-2');
        $this->createBlogPost(execute: 'Test 3', title: 'Test 3', slug: 'test-3');
        $this->createBlogPost(execute: 'Test 4', title: 'Test 4', slug: 'test-4');
        $found = BlogPost::findAll();
        self::assertCount(4, iterator_to_array($found));
    }

    public function testFindPublic(): void
    {
        $this->createBlogPost(execute: 'Test 1', title: 'Test 1', slug: 'test-1');
        $this->createBlogPost(execute: 'Test 2', title: 'Test 2', slug: 'test-2');
        $this->createBlogPost(execute: 'Test 3', title: 'Test 3', slug: 'test-3');
        $public = $this->createBlogPost(execute: false, withHeaderImage: 'test-4', title: 'Test 4', slug: 'test-4');
        $public->public = true;
        $public->create();
        $found = BlogPost::findPublicPosts();
        self::assertCount(1, iterator_to_array($found));
    }

    public function testFindAllNoneCreated(): void
    {
        $found = BlogPost::findAll();
        self::assertCount(0, iterator_to_array($found));
    }

    public function testFindById(): void
    {
        $post = $this->createBlogPost();
        $foundPost = BlogPost::findById($post->id);

        self::assertEquals($post->format(), $foundPost->format());
    }

    public function testFindByIdNotExistent(): void
    {
        $this->createBlogPost();
        $foundPost = BlogPost::findById(-100);

        self::assertNull($foundPost);
    }

    public function testUpdate(): void
    {
        $post = $this->createBlogPost();
        $post->title = 'Start';
        $post->update();

        $foundPost = BlogPost::findById($post->id);
        self::assertEquals($post->format(), $foundPost->format());
    }

    public function testGetSectionsNoSections(): void
    {
        $post = $this->createBlogPost();

        $sections = $post->getSections();
        self::assertCount(0, iterator_to_array($sections));
    }

    public function testGetCreator(): void
    {
        $post = $this->createBlogPost();
        $creator = $post->getCreator();
        self::assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testReplaceSectionsEmptyArray(): void
    {
        $post = $this->createBlogPost();
        $post->replaceSections([
            ['html' => 'Test section'],
        ]);

        self::assertCount(1, iterator_to_array($post->getSections()));

        $post->replaceSections([]);
        self::assertCount(0, iterator_to_array($post->getSections()));
    }

    public function testReplaceSectionsCreateSections(): void
    {
        $post = $this->createBlogPost();
        $post->replaceSections([
            ['html' => 'Test section'],
        ]);

        self::assertCount(1, iterator_to_array($post->getSections()));

        $file = $this->createFile();
        $gallery = $this->createGallery();
        $post->replaceSections([
            ['html' => 'Test section'],
            ['file' => $file->id],
            ['file' => $file->id, 'link' => 'https://google.com'],
            ['gallery' => $gallery->id],
        ]);

        $sections = $post->getSections();
        self::assertCount(4, iterator_to_array($sections));

        $sections = $post->getSections();

        $section = $sections->current();
        self::assertEquals('Test section', $section->html);
        self::assertEquals(0, $section->position);
        $sections->next();

        $section = $sections->current();
        self::assertEquals($file->id, $section->fileId);
        self::assertEquals(1, $section->position);
        $sections->next();

        $section = $sections->current();
        self::assertEquals($file->id, $section->fileId);
        self::assertEquals('https://google.com', $section->link);
        self::assertEquals(2, $section->position);
        $sections->next();

        $section = $sections->current();
        self::assertEquals($gallery->id, $section->galleryId);
        self::assertEquals(3, $section->position);
        $sections->next();
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = 'Gallery';
        $gallery->create();

        return $gallery;
    }
}
