<?php

namespace Jinya\Tests\Web\Actions\Frontend;

use App\Database\BlogPost;
use App\Tests\FrontTestCase;
use App\Web\Actions\Frontend\GetBlogFrontAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetBlogFrontActionTest extends FrontTestCase
{

    public function test__invoke(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->public = true;
        $post->slug = Uuid::uuid();
        $post->create();

        $_SERVER['REQUEST_URI'] = '/' . $post->slug;
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetBlogFrontAction();
        $result = $action($request, $response, ['slug' => $post->slug]);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokePostNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/' . Uuid::uuid();
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new GetBlogFrontAction();
        $result = $action($request, $response, ['slug' => Uuid::uuid()]);

        self::assertEquals(404, $result->getStatusCode());
    }
}
