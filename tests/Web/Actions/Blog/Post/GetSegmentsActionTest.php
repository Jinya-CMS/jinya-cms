<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\BlogPostSegment;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Blog\Post\GetSegmentsAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetSegmentsActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $post = new BlogPost();
        $post->title = 'Title';
        $post->slug = 'slug';
        $post->create();

        $segment = new BlogPostSegment();
        $segment->position = 1;
        $segment->blogPostId = $post->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetSegmentsAction();
        $result = $action($request, $response, ['id' => $post->getIdAsInt()]);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function test__invokePostNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetSegmentsAction();
        $action($request, $response, ['id' => -1]);
    }
}
