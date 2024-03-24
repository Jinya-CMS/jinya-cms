<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Tests\DatabaseAwareTestCase;
use Error;

class BlogCategoryTest extends DatabaseAwareTestCase
{
    public function testFindById(): void
    {
        $cat = $this->createBlogCategory();
        $foundCat = BlogCategory::findById($cat->id);

        $this->assertEquals($cat, $foundCat);
    }

    private function createBlogCategory(
        bool $execute = true,
        string $name = 'Test category',
        string $description = 'Test category'
    ): BlogCategory {
        $cat = new BlogCategory();
        $cat->description = $description;
        $cat->name = $name;
        if ($execute) {
            $cat->create();
        }

        return $cat;
    }

    public function testFindByIdNotExistent(): void
    {
        $foundCat = BlogCategory::findById(-1);

        $this->assertNull($foundCat);
    }

    public function testUpdate(): void
    {
        $cat = $this->createBlogCategory();
        $cat->name = 'New category';
        $cat->update();

        $foundCat = BlogCategory::findById($cat->id);
        $this->assertEquals($cat, $foundCat);
    }

    public function testUpdateNotExistent(): void
    {
        $cat = $this->createBlogCategory(false);
        $cat->name = 'New category';
        try {
            $cat->update();
        } catch (Error $error) {
            self::assertTrue(true);
        }
    }

    public function testFormatWithParent(): void
    {
        $cat = $this->createBlogCategory();
        $catWithParent = $this->createBlogCategory(false, name: 'Cat with parent');
        $catWithParent->parentId = $cat->id;
        $catWithParent->create();

        self::assertEquals($catWithParent->format(), [
            'name' => $catWithParent->name,
            'description' => $catWithParent->description,
            'id' => $catWithParent->id,
            'webhookEnabled' => false,
            'webhookUrl' => '',
            'parent' => [
                'name' => $cat->name,
                'description' => $cat->description,
                'id' => $cat->id,
                'parent' => null,
                'webhookEnabled' => false,
                'webhookUrl' => '',
            ],
        ]);
    }

    public function testFormatWithoutParent(): void
    {
        $cat = $this->createBlogCategory();

        self::assertEquals($cat->format(), [
            'name' => $cat->name,
            'description' => $cat->description,
            'id' => $cat->id,
            'parent' => null,
            'webhookEnabled' => false,
            'webhookUrl' => '',
        ]);
    }

    public function testFindAll(): void
    {
        $this->createBlogCategory(name: 'Test title 1', description: 'Test decription 1');
        $this->createBlogCategory(name: 'Test title 2', description: 'Test decription 2');
        $this->createBlogCategory(name: 'Test title 3', description: 'Test decription 3');
        $this->createBlogCategory(name: 'Test title 4', description: 'Test decription 4');

        $all = BlogCategory::findAll();
        self::assertCount(4, iterator_to_array($all));
    }

    public function testGetParent(): void
    {
        $cat = $this->createBlogCategory();
        $catWithParent = $this->createBlogCategory(false, name: 'Cat with parent');
        $catWithParent->parentId = $cat->id;
        $catWithParent->create();

        $this->assertEquals($cat, $catWithParent->getParent());
    }

    public function testGetParentIsNull(): void
    {
        $cat = $this->createBlogCategory();

        $this->assertNull($cat->getParent());
    }

    public function testCreate(): void
    {
        $cat = $this->createBlogCategory(false);
        $cat->create();

        $foundCat = BlogCategory::findById($cat->id);
        $this->assertEquals($cat, $foundCat);
    }

    public function testDelete(): void
    {
        $cat = $this->createBlogCategory();
        $cat->delete();

        $this->assertNull(BlogCategory::findById($cat->id));
    }

    public function testDeleteNotExistent(): void
    {
        $cat = $this->createBlogCategory(false);
        try {
            $cat->delete();
        } catch (Error $error) {
            self::assertTrue(true);
        }
    }

    public function testGetBlogPostsNoPostsIncludeChildren(): void
    {
        $cat = $this->createBlogCategory();
        $posts = $cat->getBlogPosts(true);
        self::assertCount(0, iterator_to_array($posts));
    }

    public function testGetBlogPostsNoPostsDontIncludeChildren(): void
    {
        $cat = $this->createBlogCategory();
        $posts = $cat->getBlogPosts(false);
        self::assertCount(0, iterator_to_array($posts));
    }

    public function testGetBlogPostsPostsIncludeChildren(): void
    {
        $cat = $this->createBlogCategory();
        $catWithParent = $this->createBlogCategory(false, name: 'Cat with parent');
        $catWithParent->parentId = $cat->id;
        $catWithParent->create();

        $catWithParent2 = $this->createBlogCategory(false, name: 'Cat with parent 2');
        $catWithParent2->parentId = $catWithParent->id;
        $catWithParent2->create();

        $this->createBlogPost('Test 1', 'test-1', $cat->id);
        $this->createBlogPost('Test 2', 'test-2', $catWithParent->id);
        $this->createBlogPost('Test 3', 'test-3', $catWithParent2->id);

        $posts = $cat->getBlogPosts(true);
        self::assertCount(3, iterator_to_array($posts));
    }

    public function createBlogPost(string $title, string $slug, int $categoryId, bool $public = true): BlogPost
    {
        $blogPost = new BlogPost();
        $blogPost->title = $title;
        $blogPost->slug = $slug;
        $blogPost->creatorId = CurrentUser::$currentUser->id;
        $blogPost->categoryId = $categoryId;
        $blogPost->public = $public;

        $blogPost->create();

        return $blogPost;
    }

    public function testGetBlogPostsPostsDontIncludeChildren(): void
    {
        $cat = $this->createBlogCategory();
        $catWithParent = $this->createBlogCategory(false, name: 'Cat with parent');
        $catWithParent->parentId = $cat->id;
        $catWithParent->create();

        $catWithParent2 = $this->createBlogCategory(false, name: 'Cat with parent 2');
        $catWithParent2->parentId = $catWithParent->id;
        $catWithParent2->create();

        $this->createBlogPost('Test 1', 'test-1', $cat->id);
        $this->createBlogPost('Test 2', 'test-2', $catWithParent->id);
        $this->createBlogPost('Test 3', 'test-3', $catWithParent2->id);

        $posts = $cat->getBlogPosts(false);
        self::assertCount(1, iterator_to_array($posts));
    }

    public function testGetBlogPostsPostsDontIncludeChildrenOnlyPublic(): void
    {
        $cat = $this->createBlogCategory();

        $this->createBlogPost('Test 1', 'test-1', $cat->id, false);
        $this->createBlogPost('Test 2', 'test-2', $cat->id, true);
        $this->createBlogPost('Test 3', 'test-3', $cat->id, true);

        $posts = $cat->getBlogPosts(false, true);
        self::assertCount(2, iterator_to_array($posts));
    }
}
