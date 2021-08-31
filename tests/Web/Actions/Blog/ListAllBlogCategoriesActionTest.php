<?php

namespace Jinya\Tests\Web\Actions\Blog;

use App\Database\BlogCategory;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Category\ListAllBlogCategoriesAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ListAllBlogCategoriesActionTest extends TestCase
{

    /**
     * @var array<BlogCategory>
     */
    private array $categories = [];

    public function setUp(): void
    {
        $this->categories = [];
        for ($i = 0; $i < random_int(5, 20); $i++) {
            $cat = new BlogCategory();
            $cat->description = Uuid::uuid();
            $cat->name = Uuid::uuid();
            $cat->create();
            $this->categories[] = $cat;
        }
    }

    public function testAction(): void
    {
        $action = new ListAllBlogCategoriesAction();
        $action->response = new Response();
        $response = $action->action();
        $this->assertEquals(Action::HTTP_OK, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $this->assertNotEmpty($body);

        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($json['items']);
        $this->assertCount(count($this->categories), $json['items']);

        $items = $json['items'];
        $first = $items[0];
        $this->assertEquals($this->categories[0]->format(), $first);
    }

    public function testActionByKeyword(): void
    {
        $action = new ListAllBlogCategoriesAction();
        $action->response = new Response();
        $action->queryParams = ['keyword' => $this->categories[0]->description];
        $response = $action->action();
        $this->assertEquals(Action::HTTP_OK, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $this->assertNotEmpty($body);

        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($json['items']);
        $this->assertCount(1, $json['items']);

        $items = $json['items'];
        $first = $items[0];
        $this->assertEquals($this->categories[0]->format(), $first);
    }
}
