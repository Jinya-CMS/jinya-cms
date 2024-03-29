<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Tests\DatabaseAwareTestCase;

class BlogCategoryTest extends DatabaseAwareTestCase
{

    public function testFindById(): void
    {
        $cat = $this->createBlogCategory();
        $foundCat = BlogCategory::findById($cat->getIdAsInt());

        $this->assertEquals($cat, $foundCat);
    }

    private function createBlogCategory(bool $execute = true, string $name = 'Test category', string $description = 'Test category'): BlogCategory
    {
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

    public function testFindByKeyword(): void
    {
        $this->createBlogCategory(name: 'Test title 1', description: 'Test decription 1');
        $this->createBlogCategory(name: 'Test title 2', description: 'Test decription 2');
        $this->createBlogCategory(name: 'Test title 3', description: 'Test decription 3');
        $this->createBlogCategory(name: 'Test title 4', description: 'Test decription 4');

        $found = BlogCategory::findByKeyword('title');
        $this->assertCount(4, iterator_to_array($found));
    }

    public function testFindByKeywordOneResult(): void
    {
        $this->createBlogCategory(name: 'Test title 1', description: 'Test decription 1');
        $this->createBlogCategory(name: 'Test title 2', description: 'Test decription 2');
        $this->createBlogCategory(name: 'Test title 3', description: 'Test decription 3');
        $this->createBlogCategory(name: 'Test title 4', description: 'Test decription 4');

        $found = BlogCategory::findByKeyword('title 1');
        $result = iterator_to_array($found);
        $this->assertCount(1, $result);
        $this->assertEquals($result[0]->name, 'Test title 1');
    }

    public function testUpdate(): void
    {
        $cat = $this->createBlogCategory();
        $cat->name = 'New category';
        $cat->update();

        $foundCat = BlogCategory::findById($cat->getIdAsInt());
        $this->assertEquals($cat, $foundCat);
    }

    public function testUpdateNotExistent(): void
    {
        $cat = $this->createBlogCategory(false);
        $cat->name = 'New category';
        $cat->update();

        $foundCat = BlogCategory::findById($cat->getIdAsInt());
        $this->assertNull($foundCat);
    }

    public function testFormatWithParent(): void
    {
        $cat = $this->createBlogCategory();
        $catWithParent = $this->createBlogCategory(false, name: 'Cat with parent');
        $catWithParent->parentId = $cat->getIdAsInt();
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
        $catWithParent->parentId = $cat->getIdAsInt();
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

        $foundCat = BlogCategory::findById($cat->getIdAsInt());
        $this->assertEquals($cat, $foundCat);
    }

    public function testDelete(): void
    {
        $cat = $this->createBlogCategory();
        $cat->delete();

        $this->assertNull(BlogCategory::findById($cat->getIdAsInt()));
    }

    public function testDeleteNotExistent(): void
    {
        $cat = $this->createBlogCategory(false);
        $cat->delete();

        $this->assertNull(BlogCategory::findById($cat->getIdAsInt()));
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
        $catWithParent->parentId = $cat->getIdAsInt();
        $catWithParent->create();

        $catWithParent2 = $this->createBlogCategory(false, name: 'Cat with parent 2');
        $catWithParent2->parentId = $catWithParent->getIdAsInt();
        $catWithParent2->create();

        $this->createBlogPost('Test 1', 'test-1', $cat->getIdAsInt());
        $this->createBlogPost('Test 2', 'test-2', $catWithParent->getIdAsInt());
        $this->createBlogPost('Test 3', 'test-3', $catWithParent2->getIdAsInt());

        $posts = $cat->getBlogPosts(true);
        self::assertCount(3, iterator_to_array($posts));
    }

    public function createBlogPost(string $title, string $slug, int $categoryId, bool $public = true): BlogPost
    {
        $blogPost = new BlogPost();
        $blogPost->title = $title;
        $blogPost->slug = $slug;
        $blogPost->creatorId = CurrentUser::$currentUser->getIdAsInt();
        $blogPost->categoryId = $categoryId;
        $blogPost->public = $public;

        $blogPost->create();

        return $blogPost;
    }

    public function testGetBlogPostsPostsDontIncludeChildren(): void
    {
        $cat = $this->createBlogCategory();
        $catWithParent = $this->createBlogCategory(false, name: 'Cat with parent');
        $catWithParent->parentId = $cat->getIdAsInt();
        $catWithParent->create();

        $catWithParent2 = $this->createBlogCategory(false, name: 'Cat with parent 2');
        $catWithParent2->parentId = $catWithParent->getIdAsInt();
        $catWithParent2->create();

        $this->createBlogPost('Test 1', 'test-1', $cat->getIdAsInt());
        $this->createBlogPost('Test 2', 'test-2', $catWithParent->getIdAsInt());
        $this->createBlogPost('Test 3', 'test-3', $catWithParent2->getIdAsInt());

        $posts = $cat->getBlogPosts(false);
        self::assertCount(1, iterator_to_array($posts));
    }

    public function testGetBlogPostsPostsDontIncludeChildrenOnlyPublic(): void
    {
        $cat = $this->createBlogCategory();

        $this->createBlogPost('Test 1', 'test-1', $cat->getIdAsInt(), false);
        $this->createBlogPost('Test 2', 'test-2', $cat->getIdAsInt(), true);
        $this->createBlogPost('Test 3', 'test-3', $cat->getIdAsInt(), true);

        $posts = $cat->getBlogPosts(false, true);
        self::assertCount(2, iterator_to_array($posts));
    }
}
