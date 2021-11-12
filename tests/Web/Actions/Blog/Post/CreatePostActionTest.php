<?php

namespace Jinya\Tests\Web\Actions\Blog\Post;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\File;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Post\CreatePostAction;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{

    public function testActionAllFields(): void
    {
        $cat = new BlogCategory();
        $cat->name = Uuid::uuid();
        $cat->create();

        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $action = new CreatePostAction();
        $actionBody = [
            'slug' => Uuid::uuid(),
            'title' => Uuid::uuid(),
            'categoryId' => $cat->getIdAsInt(),
            'headerImageId' => $file->getIdAsInt(),
            'public' => true,
        ];
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/post'))->withParsedBody($actionBody);
        $response = $action($request, new Response(), []);
        $this->assertEquals(Action::HTTP_CREATED, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $post = BlogPost::findById($json['id']);
        $this->assertEquals($post->categoryId, $cat->getIdAsInt());
        $this->assertEquals($post->headerImageId, $file->getIdAsInt());
        $this->assertEquals($post->public, $actionBody['public']);
        $this->assertEquals($post->slug, $actionBody['slug']);
        $this->assertEquals($post->title, $actionBody['title']);
    }

    public function testActionRequiredFields(): void
    {
        $action = new CreatePostAction();
        $actionBody = [
            'slug' => Uuid::uuid(),
            'title' => Uuid::uuid(),
        ];
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/post'))->withParsedBody($actionBody);
        $response = $action($request, new Response(), []);
        $this->assertEquals(Action::HTTP_CREATED, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $post = BlogPost::findById($json['id']);
        $this->assertEquals($post->categoryId, null);
        $this->assertEquals($post->headerImageId, null);
        $this->assertEquals($post->public, false);
        $this->assertEquals($post->slug, $actionBody['slug']);
        $this->assertEquals($post->title, $actionBody['title']);
    }

    public function testActionDuplicateSlug(): void
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('Slug exists');
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();

        $action = new CreatePostAction();
        $actionBody = [
            'slug' => $post->slug,
            'title' => Uuid::uuid(),
        ];
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/post'))->withParsedBody($actionBody);
        $action($request, new Response(), []);
    }

    public function testActionDuplicateTitle(): void
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('Title exists');
        $post = new BlogPost();
        $post->title = Uuid::uuid();
        $post->slug = Uuid::uuid();
        $post->create();

        $action = new CreatePostAction();
        $actionBody = [
            'title' => $post->title,
            'slug' => Uuid::uuid(),
        ];
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/post'))->withParsedBody($actionBody);
        $action($request, new Response(), []);
    }
}
