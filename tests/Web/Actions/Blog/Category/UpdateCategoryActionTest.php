<?php

namespace Jinya\Tests\Web\Actions\Blog\Category;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Category\UpdateCategoryAction;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpBadRequestException;

class UpdateCategoryActionTest extends TestCase
{
    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws HttpBadRequestException
     * @throws InvalidQueryException
     */
    public function testActionDescriptionOnly(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $actionBody = [
            'description' => Uuid::uuid(),
        ];

        $action = new UpdateCategoryAction();
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/category/' . $category->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $category->id]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());

        $savedCategory = BlogCategory::findById($category->getIdAsInt());
        $this->assertNotNull($savedCategory);
        $this->assertEquals($actionBody['description'], $savedCategory->description);
        $this->assertEquals($category->name, $savedCategory->name);
    }

    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws HttpBadRequestException
     * @throws InvalidQueryException
     */
    public function testActionNameOnly(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $actionBody = [
            'name' => Uuid::uuid(),
        ];

        $action = new UpdateCategoryAction();
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/category/' . $category->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $category->id]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());

        $savedCategory = BlogCategory::findById($category->getIdAsInt());
        $this->assertNotNull($savedCategory);
        $this->assertEquals($actionBody['name'], $savedCategory->name);
        $this->assertEquals($category->description, $savedCategory->description);
    }

    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws HttpBadRequestException
     * @throws InvalidQueryException
     */
    public function testActionParentIdOnly(): void
    {
        $parentCategory = new BlogCategory();
        $parentCategory->name = Uuid::uuid();
        $parentCategory->create();

        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $actionBody = [
            'parentId' => $parentCategory->getIdAsInt(),
        ];

        $action = new UpdateCategoryAction();
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/category/' . $category->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $category->id]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());

        $savedCategory = BlogCategory::findById($category->getIdAsInt());
        $this->assertNotNull($savedCategory);
        $this->assertEquals($actionBody['parentId'], $savedCategory->parentId);
        $this->assertEquals($category->name, $savedCategory->name);
        $this->assertEquals($category->description, $savedCategory->description);
    }

    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws HttpBadRequestException
     * @throws InvalidQueryException
     */
    public function testActionNotMappedFields(): void
    {
        $parentCategory = new BlogCategory();
        $parentCategory->name = Uuid::uuid();
        $parentCategory->create();

        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $actionBody = [
            'test' => $parentCategory->getIdAsInt(),
            'test2' => $parentCategory->getIdAsInt(),
        ];

        $action = new UpdateCategoryAction();
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/category/' . $category->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $category->id]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());

        $savedCategory = BlogCategory::findById($category->getIdAsInt());
        $this->assertNotNull($savedCategory);
        $this->assertEquals($category->parentId, $savedCategory->parentId);
        $this->assertEquals($category->name, $savedCategory->name);
        $this->assertEquals($category->description, $savedCategory->description);
    }

    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws HttpBadRequestException
     * @throws InvalidQueryException
     */
    public function testActionNoFields(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $actionBody = [];

        $action = new UpdateCategoryAction();
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/category/' . $category->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $category->id]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());

        $savedCategory = BlogCategory::findById($category->getIdAsInt());
        $this->assertNotNull($savedCategory);
        $this->assertEquals($category->parentId, $savedCategory->parentId);
        $this->assertEquals($category->name, $savedCategory->name);
        $this->assertEquals($category->description, $savedCategory->description);
    }

    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws HttpBadRequestException
     * @throws InvalidQueryException
     */
    public function testActionAllFields(): void
    {
        $parentCategory = new BlogCategory();
        $parentCategory->name = Uuid::uuid();
        $parentCategory->create();

        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->description = Uuid::uuid();
        $category->create();

        $actionBody = [
            'name' => Uuid::uuid(),
            'description' => Uuid::uuid(),
            'parentId' => $parentCategory->getIdAsInt(),
        ];

        $action = new UpdateCategoryAction();
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/category/' . $category->id))->withParsedBody($actionBody);
        $response = $action($request, new Response(), ['id' => $category->id]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());

        $savedCategory = BlogCategory::findById($category->getIdAsInt());
        $this->assertNotNull($savedCategory);
        $this->assertEquals($actionBody['parentId'], $savedCategory->parentId);
        $this->assertEquals($actionBody['name'], $savedCategory->name);
        $this->assertEquals($actionBody['description'], $savedCategory->description);
    }

    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws HttpBadRequestException
     * @throws InvalidQueryException
     */
    public function testActionNoResult(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Category not found');

        $actionBody = [
            'name' => Uuid::uuid(),
            'description' => Uuid::uuid(),
        ];

        $action = new UpdateCategoryAction();
        $request = (new ServerRequest(JinyaAction::POST, '/api/blog/category/-1'))->withParsedBody($actionBody);
        $action($request, new Response(), ['id' => -1]);
    }
}
