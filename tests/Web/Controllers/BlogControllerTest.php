<?php

namespace Jinya\Tests\Web\Controllers;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\BlogController;
use Faker\Factory;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class BlogControllerTest extends DatabaseAwareTestCase
{
    private function getController(array $body): BlogController
    {
        $controller = new BlogController();
        $controller->request = (new ServerRequest('', ''))->withParsedBody($body);
        $controller->body = $body;

        return $controller;
    }

    public function testGetPostsByCategory(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $withCategory = 0;
        $postsWithCat = [];
        $faker = Factory::create();
        for ($i = 0; $i < random_int(5, 20); $i++) {
            $post = new BlogPost();
            $post->slug = Uuid::uuid();
            $post->title = Uuid::uuid();
            if ($faker->boolean()) {
                $post->categoryId = $category->id;
                $withCategory++;
            }
            $post->create();
            if ($post->categoryId) {
                $postsWithCat[] = $post;
            }
        }

        $controller = $this->getController([]);
        $result = $controller->getPostsByCategory($category->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNotEmpty($body['items']);
        self::assertCount($withCategory, $body['items']);

        $items = $body['items'];
        $first = $items[0];
        self::assertEquals($postsWithCat[0]->format(), $first);
    }

    public function testGetPostsByCategoryCategoryNotExists(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getPostsByCategory(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Blog category not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testGetSections(): void
    {
        $post = new BlogPost();
        $post->title = 'Title';
        $post->slug = 'slug';
        $post->create();

        $post->replaceSections([
            [
                'html' => 'Test'
            ]
        ]);

        $controller = $this->getController([]);
        $result = $controller->getSections($post->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, $body);
    }

    public function testGetSectionsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getSections(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Blog post not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testReplaceSegments(): void
    {
        $post = new BlogPost();
        $post->title = 'Title';
        $post->slug = 'slug';
        $post->create();

        $controller = $this->getController([
            [
                'html' => 'Test'
            ]
        ]);
        $result = $controller->replaceSections($post->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testReplaceSegmentsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->replaceSections(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Blog post not found',
                'type' => 'not-found',
            ],
        ], $body);
    }
}
