<?php

namespace Jinya\Tests\Database;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\BlogPostSection;
use App\Database\File;
use App\Database\Gallery;
use App\Tests\DatabaseAwareTestCase;
use Faker\Provider\Uuid;
use RuntimeException;

class BlogPostSectionTest extends DatabaseAwareTestCase
{
    public function testGetBlogPost(): void
    {
        $post = $this->createBlogPost();
        $section = $this->createBlogPostSection(1, $post->id);

        $sectionPost = $section->getBlogPost();
        $this->assertEquals($post->format(), $sectionPost->format());
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

    private function createBlogPostSection(int $position, int $blogPostId, bool $execute = true, string $html = null, int $galleryId = null, int $fileId = null): BlogPostSection
    {
        $section = new BlogPostSection();
        $section->blogPostId = $blogPostId;
        $section->html = $html;
        $section->galleryId = $galleryId;
        $section->fileId = $fileId;
        $section->position = $position;
        $section->id = 0;

        return $section;
    }

    public function testFormatHtml(): void
    {
        $post = $this->createBlogPost();
        $section = $this->createBlogPostSection(0, $post->id, html: 'Test');
        $this->assertEquals([
            'position' => $section->position,
            'id' => $section->id,
            'html' => $section->html,
        ], $section->format());
    }

    public function testFormatFile(): void
    {
        $file = $this->createFile();
        $post = $this->createBlogPost();
        $section = $this->createBlogPostSection(0, $post->id, fileId: $file->id);
        $this->assertEquals([
            'position' => $section->position,
            'id' => $section->id,
            'file' => [
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
                'path' => $file->path,
            ],
            'link' => $section->link,
        ], $section->format());
    }

    public function testGetFile(): void
    {
        $file = $this->createFile();
        $post = $this->createBlogPost();
        $section = $this->createBlogPostSection(0, $post->id, fileId: $file->id);
        $this->assertEquals($file->name, $section->getFile()->name);
        self::assertNull($section->getGallery());
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
        $section = $this->createBlogPostSection(0, $post->id, galleryId: $gallery->id);
        $this->assertEquals([
            'position' => $section->position,
            'id' => $section->id,
            'gallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'type' => $gallery->type,
                'orientation' => $gallery->orientation,
            ],
        ], $section->format());
    }

    public function testGetGallery(): void
    {
        $gallery = $this->createGallery();
        $post = $this->createBlogPost();
        $section = $this->createBlogPostSection(0, $post->id, galleryId: $gallery->id);
        $this->assertEquals($gallery->name, $section->getGallery()->name);
        self::assertNull($section->getFile());
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
