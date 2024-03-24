<?php

namespace Jinya\Tests\Database;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\BlogPostSegment;
use App\Database\File;
use App\Database\Gallery;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use RuntimeException;

class BlogPostSegmentTest extends DatabaseAwareTestCase
{
    public function testGetBlogPost(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(1, $post->id);

        $segmentPost = $segment->getBlogPost();
        $this->assertEquals($post->format(), $segmentPost->format());
    }

    private function createBlogPost(): BlogPost
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->categoryId = $this->createBlogCategory()->id;
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
        $segment->id = 0;

        return $segment;
    }

    public function testFormatHtml(): void
    {
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->id, html: 'Test');
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->id,
            'html' => $segment->html,
        ], $segment->format());
    }

    public function testFormatFile(): void
    {
        $file = $this->createFile();
        $post = $this->createBlogPost();
        $segment = $this->createBlogPostSegment(0, $post->id, fileId: $file->id);
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->id,
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
        $segment = $this->createBlogPostSegment(0, $post->id, fileId: $file->id);
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
        $segment = $this->createBlogPostSegment(0, $post->id, galleryId: $gallery->id);
        $this->assertEquals([
            'position' => $segment->position,
            'id' => $segment->id,
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
        $segment = $this->createBlogPostSegment(0, $post->id, galleryId: $gallery->id);
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
}
