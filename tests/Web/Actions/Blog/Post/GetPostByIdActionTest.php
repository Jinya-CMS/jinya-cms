<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Post\GetPostByIdAction;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use JsonException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpBadRequestException;

class GetPostByIdActionTest extends TestCase
{
    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws JsonException
     * @throws HttpBadRequestException
     */
    public function testGetPostExists(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();

        $action = new GetPostByIdAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/post/' . $post->getIdAsInt());
        $response = $action($request, new Response(), ['id' => $post->getIdAsInt()]);
        $response->getBody()->rewind();

        $this->assertEquals(Action::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($post->format(), json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws HttpBadRequestException
     */
    public function testGetPostNotExists(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Post not found');

        $action = new GetPostByIdAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/post/-1');
        $action($request, new Response(), ['id' => -1]);
    }
}
