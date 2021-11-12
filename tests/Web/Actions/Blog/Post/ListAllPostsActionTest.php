<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Post\ListAllPostsAction;
use App\Web\Attributes\JinyaAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class ListAllPostsActionTest extends TestCase
{

    /**
     * @var array<BlogPost>
     */
    private array $posts = [];

    public function setUp(): void
    {
        $this->posts = [];
        for ($i = 0; $i < random_int(5, 20); $i++) {
            $post = new BlogPost();
            $post->slug = Uuid::uuid();
            $post->title = Uuid::uuid();
            $post->create();
            $this->posts[] = $post;
        }
    }

    public function testAction(): void
    {
        $action = new ListAllPostsAction();
        $response = $action(new ServerRequest(JinyaAction::GET, '/api/blog/post'), new Response(), []);
        $this->assertEquals(Action::HTTP_OK, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $this->assertNotEmpty($body);

        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($json['items']);
        $this->assertCount(count($this->posts), $json['items']);

        $items = $json['items'];
        $first = $items[0];
        $this->assertEquals($this->posts[0]->format(), $first);
    }

    public function testActionByKeyword(): void
    {
        $action = new ListAllPostsAction();
        $request = (new ServerRequest(JinyaAction::GET, '/api/blog/post'))->withQueryParams(['keyword' => $this->posts[0]->title]);
        $response = $action($request, new Response(), []);
        $this->assertEquals(Action::HTTP_OK, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $this->assertNotEmpty($body);

        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($json['items']);
        $this->assertCount(1, $json['items']);

        $items = $json['items'];
        $first = $items[0];
        $this->assertEquals($this->posts[0]->format(), $first);
    }
}
