<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Blog\Post\ListPostsByCategoryAction;
use App\Web\Exceptions\NoResultException;
use Faker\Factory;
use Faker\Provider\Uuid;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use JsonException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpBadRequestException;

class ListPostsByCategoryActionTest extends DatabaseAwareTestCase
{
    private BlogCategory $category;
    private int $withCategory = 0;
    /** @var array<BlogPost> */
    private array $postsWithCat = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->category = new BlogCategory();
        $this->category->name = Uuid::uuid();
        $this->category->create();

        $faker = Factory::create();
        for ($i = 0; $i < random_int(5, 20); $i++) {
            $post = new BlogPost();
            $post->slug = Uuid::uuid();
            $post->title = Uuid::uuid();
            if ($faker->boolean()) {
                $post->categoryId = $this->category->getIdAsInt();
                $this->withCategory++;
            }
            $post->create();
            if ($post->categoryId) {
                $this->postsWithCat[] = $post;
            }
        }
    }

    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws JsonException
     * @throws HttpBadRequestException
     */
    public function testListPostsByCategoryExists(): void
    {
        $action = new ListPostsByCategoryAction();
        $request = new ServerRequest('GET', '/api/blog/category/' . $this->category->getIdAsInt() . '/post');
        $response = $action($request, new Response(), ['id' => $this->category->getIdAsInt()]);

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $this->assertNotEmpty($body);

        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($json['items']);
        $this->assertCount($this->withCategory, $json['items']);

        $items = $json['items'];
        $first = $items[0];
        $this->assertEquals($this->postsWithCat[0]->format(), $first);
    }

    /**
     * @throws HttpBadRequestException
     */
    public function testGetCategoryNotExists(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Category not found');

        $action = new ListPostsByCategoryAction();
        $request = new ServerRequest('GET', '/api/blog/category/-1/post');
        $action($request, new Response(), ['id' => -1]);
    }
}
