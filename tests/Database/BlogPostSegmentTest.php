<?php

namespace Jinya\Tests\Database;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\BlogPostSegment;
use App\Database\File;
use App\Database\Gallery;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BlogPostSegmentTest extends TestCase
{

    public function testGetBlogPost(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(1, $post->getIdAsInt());

        $segmentPost = $segment->getBlogPost();
        $this->assertEquals($post->format(), $segmentPost->format());
    }

    private function createBlogPost(): BlogPost
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->categoryId = $this->createBlogCategory()->getIdAsInt();
        $post->create();

        return $post;
    }

    private function createBlogCategory(): BlogCategory
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        return $category;
    }

    private function createBlogPostSegment(int $position, int $blogPostId, bool $execute = true, string $html = null, int $galleryId = null, int $fileId = null): BlogPostSegment
    {
        $segment = new BlogPostSegment();
        $segment->blogPostId = $blogPostId;
        $segment->html = $html;
        $segment->galleryId = $galleryId;
        $segment->fileId = $fileId;
        $segment->position = $position;
        if ($execute) {
            $segment->create();
        }

        return $segment;
    }

    public function testUpdate(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt());
        $segment->position = 5;
        $segment->update();
        $foundSegment = BlogPostSegment::findByPosition($post->getIdAsInt(), 5);
        $this->assertEquals($segment, $foundSegment);
    }

    public function testUpdateNotExistent(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), execute: false);
        $segment->position = 5;
        $segment->update();
        $foundSegment = BlogPostSegment::findByPosition($post->getIdAsInt(), 5);
        $this->assertNull($foundSegment);
    }

    public function testCreate(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), execute: false);
        $segment->create();
        $foundSegment = BlogPostSegment::findByPosition($post->getIdAsInt(), 0);
        $this->assertEquals($segment, $foundSegment);
    }

    public function testFindByPosition(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt());
        $this->createBlogPostSegment(1, $post->getIdAsInt());
        $foundSegment = BlogPostSegment::findByPosition($post->getIdAsInt(), 0);
        $this->assertEquals($segment, $foundSegment);
    }

    public function testFormatHtml(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), html: 'Test');
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->getIdAsInt(),
            'html' => $segment->html,
        ], $segment->format());
    }

    public function testFormatFile(): void
    {
        $file = $this->createFile();
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), fileId: $file->getIdAsInt());
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->getIdAsInt(),
            'file' => [
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
                'path' => $file->path,
            ],
            'link' => $segment->link,
        ], $segment->format());
    }

    public function testGetFile(): void
    {
        $file = $this->createFile();
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), fileId: $file->getIdAsInt());
        $this->assertEquals($file->name, $segment->getFile()->name);
        self::assertNull($segment->getGallery());
    }

    private function createFile(): File
    {
        $file = new File();
        $file->path = 'this-does-not-exist';
        $file->name = Uuid::uuid();
        $file->type = 'application/octet-stream';
        $file->create();

        return $file;
    }

    public function testFormatGallery(): void
    {
        $gallery = $this->createGallery();
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), galleryId: $gallery->getIdAsInt());
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->getIdAsInt(),
            'gallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'type' => $gallery->type,
                'orientation' => $gallery->orientation,
            ],
        ], $segment->format());
    }

    public function testGetGallery(): void
    {
        $gallery = $this->createGallery();
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), galleryId: $gallery->getIdAsInt());
        $this->assertEquals($gallery->name, $segment->getGallery()->name);
        self::assertNull($segment->getFile());
    }

    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->description = Uuid::uuid();
        $gallery->create();

        return $gallery;
    }

    public function testDelete(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt());
        $segment->delete();
        $foundSegment = BlogPostSegment::findByPosition($post->getIdAsInt(), 5);
        $this->assertNull($foundSegment);
    }

    public function testDeleteNotExistent(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->getIdAsInt(), execute: false);
        $segment->delete();
        $foundSegment = BlogPostSegment::findByPosition($post->getIdAsInt(), 5);
        $this->assertNull($foundSegment);
    }

    public function testMove(): void
    {
        $post = $this->createBlogPost();
        $segment1 = $this->createBlogPostSegment(0, $post->getIdAsInt());
        $segment2 = $this->createBlogPostSegment(1, $post->getIdAsInt());
        $segment3 = $this->createBlogPostSegment(2, $post->getIdAsInt());
        $segment2->move(0);

        $foundSegment1 = BlogPostSegment::findByPosition($post->getIdAsInt(), 0);
        $foundSegment2 = BlogPostSegment::findByPosition($post->getIdAsInt(), 1);
        $foundSegment3 = BlogPostSegment::findByPosition($post->getIdAsInt(), 2);

        $this->assertEquals($foundSegment1->getIdAsInt(), $segment2->getIdAsInt());
        $this->assertEquals($foundSegment2->getIdAsInt(), $segment1->getIdAsInt());
        $this->assertEquals($foundSegment3->getIdAsInt(), $segment3->getIdAsInt());
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        BlogPostSegment::findById(0);
    }

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        BlogPostSegment::findByKeyword('test');
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        BlogPostSegment::findAll();
    }
}
