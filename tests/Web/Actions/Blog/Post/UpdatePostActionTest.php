<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\File;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Post\UpdatePostAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class UpdatePostActionTest extends TestCase
{

    public function testActionAllFields(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();

        $cat = new BlogCategory();
        $cat->name = Uuid::uuid();
        $cat->create();

        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $action = new UpdatePostAction();
        $actionBody = [
            'slug' => Uuid::uuid(),
            'title' => Uuid::uuid(),
            'categoryId' => $cat->getIdAsInt(),
            'headerImageId' => $file->getIdAsInt(),
            'public' => true,
        ];
        $request = (new ServerRequest('POST', '/api/blog/post/' . $post->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $post->id]);
        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testActionNoFields(): void
    {
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();

        $action = new UpdatePostAction();
        $actionBody = [
        ];
        $request = (new ServerRequest('POST', '/api/blog/post/' . $post->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $post->id]);
        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testActionDuplicateSlug(): void
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('Slug exists');
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();
        $post2 = new BlogPost();
        $post2->title = Uuid::uuid();
        $post2->slug = Uuid::uuid();
        $post2->create();

        $action = new UpdatePostAction();
        $actionBody = [
            'slug' => $post->slug,
            'title' => Uuid::uuid(),
        ];
        $request = (new ServerRequest('POST', '/api/blog/post/' . $post2->id))->withParsedBody($actionBody);
        $action($request, new Response(), ['id' => $post2->id]);
    }

    public function testActionDuplicateTitle(): void
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('Title exists');
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();
        $post2 = new BlogPost();
        $post2->title = Uuid::uuid();
        $post2->slug = Uuid::uuid();
        $post2->create();

        $action = new UpdatePostAction();
        $actionBody = [
            'slug' => Uuid::uuid(),
            'title' => $post->title,
        ];
        $request = (new ServerRequest('POST', '/api/blog/post'))->withParsedBody($actionBody);
        $action($request, new Response(), ['id' => $post2->id]);
    }

    public function testActionNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Post not found');

        $action = new UpdatePostAction();
        $actionBody = [
        ];
        $request = (new ServerRequest('POST', '/api/blog/post/-1'))->withParsedBody($actionBody);
        $action($request, new Response(), ['id' => -1]);
    }
}
