<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\BlogPostSegment;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Gallery;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;

class BlogPostTest extends TestCase
{

    public function testCreate(): void
    {
        $post = $this->createBlogPost(false);
        $post->create();

        $foundPost = BlogPost::findById($post->getIdAsInt());
        $this->assertEquals($post->format(), $foundPost->format());
    }

    private function createBlogPost(bool $execute = true, bool $withCategory = true, bool $withHeaderImage = true, string $title = 'Test', string $slug = 'test'): BlogPost
    {
        $post = new BlogPost();
        $post->title = $title;
        $post->slug = $slug;
        if ($withHeaderImage) {
            $post->headerImageId = $this->createFile()->getIdAsInt();
        }
        if ($withCategory) {
            $post->categoryId = $this->createBlogCategory()->getIdAsInt();
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
        $this->expectException(UniqueFailedException::class);
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

        $this->assertNull(BlogPost::findById($post->getIdAsInt()));
    }

    public function testDeleteNotExistent(): void
    {
        $post = $this->createBlogPost(false);
        $post->delete();
        $this->assertTrue(true);
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
                'id' => $post->getHeaderImage()->getIdAsInt(),
                'name' => $post->getHeaderImage()->name,
                'path' => $post->getHeaderImage()->path,
            ],
            'category' => [
                'id' => $post->getCategory()->getIdAsInt(),
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
        $this->assertCount(4, $found);
    }

    public function testFindAllNoneCreated(): void
    {
        $found = BlogPost::findAll();
        $this->assertCount(0, $found);
    }

    public function testFindById(): void
    {
        $post = $this->createBlogPost();
        $foundPost = BlogPost::findById($post->getIdAsInt());

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

        $foundPost = BlogPost::findById($post->getIdAsInt());
        $this->assertEquals($post->format(), $foundPost->format());
    }

    public function testFindByKeyword(): void
    {
        $this->createBlogPost(title: 'Test 1', slug: 'test-1');
        $this->createBlogPost(title: 'Test 2', slug: 'test-2');
        $this->createBlogPost(title: 'Test 3', slug: 'test-3');
        $this->createBlogPost(title: 'Test 4', slug: 'test-4');
        $found = BlogPost::findByKeyword('test');
        $this->assertCount(4, $found);

        $found = BlogPost::findByKeyword('test-1');
        $this->assertCount(1, $found);

        $found = BlogPost::findByKeyword('Test 1');
        $this->assertCount(1, $found);

        $found = BlogPost::findByKeyword('Test 15');
        $this->assertCount(0, $found);
    }

    public function testGetSegments(): void
    {
        $post = $this->createBlogPost();
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());

        $segments = $post->getSegments();
        $this->assertCount(4, $segments);
    }

    private function createBlogPostSegment(int $blogPostId): void
    {
        $segment = new BlogPostSegment();
        $segment->blogPostId = $blogPostId;
        $segment->position = random_int(0, 10000);
        $segment->create();
    }

    public function testGetSegmentsNoSegments(): void
    {
        $post = $this->createBlogPost();

        $segments = $post->getSegments();
        $this->assertCount(0, $segments);
    }

    public function testGetCreator(): void
    {
        $post = $this->createBlogPost();
        $creator = $post->getCreator();
        $this->assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testBatchReplaceSegmentsEmptyArray(): void
    {
        $post = $this->createBlogPost();
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());

        $this->assertCount(5, $post->getSegments());

        $post->batchReplaceSegments([]);
        $this->assertCount(0, $post->getSegments());
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = 'Gallery';
        $gallery->create();

        return $gallery;
    }


    public function testBatchReplaceSegmentsCreateSegments(): void
    {
        $post = $this->createBlogPost();
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());
        $this->createBlogPostSegment($post->getIdAsInt());

        $this->assertCount(5, $post->getSegments());

        $file = $this->createFile();
        $gallery = $this->createGallery();
        $post->batchReplaceSegments([
            ['html' => 'Test segment'],
            ['file' => $file->getIdAsInt()],
            ['file' => $file->getIdAsInt(), 'link' => 'https://google.com'],
            ['gallery' => $gallery->getIdAsInt()],
        ]);

        $segments = $post->getSegments();
        $this->assertCount(4, $segments);

        $segments = $post->getSegments();

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
        $this->assertEquals('https://google.com', $segment->link);
        $this->assertEquals(2, $segment->position);
        $segments->next();

        $segment = $segments->current();
        $this->assertEquals($gallery->getIdAsInt(), $segment->galleryId);
        $this->assertEquals(3, $segment->position);
        $segments->next();
    }
}
