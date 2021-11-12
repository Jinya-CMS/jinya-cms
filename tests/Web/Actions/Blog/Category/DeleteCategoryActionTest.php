<?php

namespace Jinya\Tests\Web\Actions\Blog\Category;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Category\DeleteCategoryAction;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpBadRequestException;

class DeleteCategoryActionTest extends TestCase
{
    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws HttpBadRequestException
     */
    public function testDeleteCategoryFound(): void
    {
        $cat = new BlogCategory();
        $cat->name = Uuid::uuid();
        $cat->create();

        $action = new DeleteCategoryAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/category/' . $cat->getIdAsInt());
        $response = $action($request, new Response(), ['id' => $cat->getIdAsInt()]);

        $this->assertEquals(Action::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @throws HttpBadRequestException
     */
    public function testDeleteCategoryNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Category not found');

        $action = new DeleteCategoryAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/category/-1');
        $action($request, new Response(), ['id' => -1]);
    }
}
