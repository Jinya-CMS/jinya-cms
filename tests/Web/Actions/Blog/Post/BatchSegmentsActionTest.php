<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Blog\Post\BatchSegmentsAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class BatchSegmentsActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $post = new BlogPost();
        $post->title = 'Title';
        $post->slug = 'slug';
        $post->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['segments' => []]);
        $response = new Response();
        $action = new BatchSegmentsAction();
        $result = $action($request, $response, ['id' => $post->getIdAsInt()]);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokePostNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['segments' => []]);
        $response = new Response();
        $action = new BatchSegmentsAction();
        $action($request, $response, ['id' => -1]);
    }
}
