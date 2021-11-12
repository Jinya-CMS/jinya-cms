<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Post\DeletePostAction;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpBadRequestException;

class DeletePostActionTest extends TestCase
{
    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws HttpBadRequestException
     */
    public function testDeletePostFound(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();

        $action = new DeletePostAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/post/' . $post->getIdAsInt());
        $response = $action($request, new Response(), ['id' => $post->getIdAsInt()]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @throws HttpBadRequestException
     */
    public function testDeletePostNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Post not found');

        $action = new DeletePostAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/post/-1');
        $action($request, new Response(), ['id' => -1]);
    }
}
