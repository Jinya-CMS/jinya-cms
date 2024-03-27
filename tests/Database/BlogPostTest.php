<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\BlogPostSection;
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
        $this->assertEquals($post->format(), $foundPost->format());
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
        $this->assertEquals($post->format(), $foundPost->format());
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
        $this->assertEquals($post->format(), $foundPost->format());
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
        $this->assertEquals($post->format(), $foundPost->format());
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
        $this->assertTrue(true);
    }

    private function createBlogPost(
        bool $execute = true,
        bool $withCategory = true,
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
        if ($withCategory) {
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

    public function testCreateDuplicate(): void
    {
        $this->expectException(PDOException::class);
        $this->createBlogPost();
        $this->createBlogPost();
    }

    public function testGetHeaderImage(): void
    {
        $post = $this->createBlogPost();
        $this->assertNotNull($post->getHeaderImage());
        $this->assertEquals(File::findById($post->headerImageId), $post->getHeaderImage());
    }

    public function testGetHeaderImageEmpty(): void
    {
        $post = $this->createBlogPost(withHeaderImage: false);
        $this->assertNull($post->getHeaderImage());
    }

    public function testDelete(): void
    {
        $post = $this->createBlogPost();
        $post->delete();

        $this->assertNull(BlogPost::findById($post->id));
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

        $this->assertEquals($post->format(), $foundPost->format());
    }

    public function testFindBySlugNotExistent(): void
    {
        $this->createBlogPost();
        $foundPost = BlogPost::findBySlug('start');

        $this->assertNull($foundPost);
    }

    public function testFormat(): void
    {
        $post = $this->createBlogPost();
        $this->assertEquals([
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
        $this->assertEquals($category, $post->getCategory());
    }

    public function testFindAll(): void
    {
        $this->createBlogPost(title: 'Test 1', slug: 'test-1');
        $this->createBlogPost(title: 'Test 2', slug: 'test-2');
        $this->createBlogPost(title: 'Test 3', slug: 'test-3');
        $this->createBlogPost(title: 'Test 4', slug: 'test-4');
        $found = BlogPost::findAll();
        $this->assertCount(4, iterator_to_array($found));
    }

    public function testFindPublic(): void
    {
        $this->createBlogPost(title: 'Test 1', slug: 'test-1');
        $this->createBlogPost(title: 'Test 2', slug: 'test-2');
        $this->createBlogPost(title: 'Test 3', slug: 'test-3');
        $public = $this->createBlogPost(execute: false, title: 'Test 4', slug: 'test-4');
        $public->public = true;
        $public->create();
        $found = BlogPost::findPublicPosts();
        $this->assertCount(1, iterator_to_array($found));
    }

    public function testFindAllNoneCreated(): void
    {
        $found = BlogPost::findAll();
        $this->assertCount(0, iterator_to_array($found));
    }

    public function testFindById(): void
    {
        $post = $this->createBlogPost();
        $foundPost = BlogPost::findById($post->id);

        $this->assertEquals($post->format(), $foundPost->format());
    }

    public function testFindByIdNotExistent(): void
    {
        $this->createBlogPost();
        $foundPost = BlogPost::findById(-100);

        $this->assertNull($foundPost);
    }

    public function testUpdate(): void
    {
        $post = $this->createBlogPost();
        $post->title = 'Start';
        $post->update();

        $foundPost = BlogPost::findById($post->id);
        $this->assertEquals($post->format(), $foundPost->format());
    }

    public function testGetSectionsNoSections(): void
    {
        $post = $this->createBlogPost();

        $sections = $post->getSections();
        $this->assertCount(0, iterator_to_array($sections));
    }

    public function testGetCreator(): void
    {
        $post = $this->createBlogPost();
        $creator = $post->getCreator();
        $this->assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testReplaceSectionsEmptyArray(): void
    {
        $post = $this->createBlogPost();
        $post->replaceSections([
            ['html' => 'Test section'],
        ]);

        $this->assertCount(1, iterator_to_array($post->getSections()));

        $post->replaceSections([]);
        $this->assertCount(0, iterator_to_array($post->getSections()));
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
        $post = $this->createBlogPost();
        $post->replaceSections([
            ['html' => 'Test section'],
        ]);

        $this->assertCount(1, iterator_to_array($post->getSections()));

        $file = $this->createFile();
        $gallery = $this->createGallery();
        $post->replaceSections([
            ['html' => 'Test section'],
            ['file' => $file->id],
            ['file' => $file->id, 'link' => 'https://google.com'],
            ['gallery' => $gallery->id],
        ]);

        $sections = $post->getSections();
        $this->assertCount(4, iterator_to_array($sections));

        $sections = $post->getSections();

        $section = $sections->current();
        $this->assertEquals('Test section', $section->html);
        $this->assertEquals(0, $section->position);
        $sections->next();

        $section = $sections->current();
        $this->assertEquals($file->id, $section->fileId);
        $this->assertEquals(1, $section->position);
        $sections->next();

        $section = $sections->current();
        $this->assertEquals($file->id, $section->fileId);
        $this->assertEquals('https://google.com', $section->link);
        $this->assertEquals(2, $section->position);
        $sections->next();

        $section = $sections->current();
        $this->assertEquals($gallery->id, $section->galleryId);
        $this->assertEquals(3, $section->position);
        $sections->next();
    }
}
