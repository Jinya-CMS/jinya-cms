<?php

namespace Jinya\Tests\Web\Actions\Blog\Category;

use App\Database\BlogCategory;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Category\CreateCategoryAction;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class CreateCategoryActionTest extends TestCase
{

    public function testActionAllFields(): void
    {
        $parentCat = new BlogCategory();
        $parentCat->description = Uuid::uuid();
        $parentCat->name = Uuid::uuid();
        $parentCat->create();

        $action = new CreateCategoryAction();
        $action->response = new Response();
        $action->body = [
            'name' => Uuid::uuid(),
            'description' => Uuid::uuid(),
            'parentId' => $parentCat->getIdAsInt(),
        ];
        $response = $action->action();
        $this->assertEquals(Action::HTTP_CREATED, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $cat = BlogCategory::findById($json['id']);
        $this->assertEquals($cat->format(), $json);
    }

    public function testActionRequiredFields(): void
    {
        $action = new CreateCategoryAction();
        $action->response = new Response();
        $action->body = [
            'name' => Uuid::uuid(),
        ];
        $response = $action->action();
        $this->assertEquals(Action::HTTP_CREATED, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $cat = BlogCategory::findById($json['id']);
        $this->assertEquals($cat->format(), $json);
    }

    public function testActionDuplicateName(): void
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('Name exists');
        $action = new CreateCategoryAction();
        $action->request = new ServerRequest(JinyaAction::POST, '/blog/category');
        $action->response = new Response();
        $action->body = [
            'name' => Uuid::uuid(),
        ];
        $response = $action->action();
        $this->assertEquals(Action::HTTP_CREATED, $response->getStatusCode());

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $cat = BlogCategory::findById($json['id']);
        $this->assertEquals($cat->format(), $json);

        $action->action();
    }
}
