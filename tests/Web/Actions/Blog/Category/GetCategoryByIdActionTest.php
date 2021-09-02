<?php

namespace Jinya\Tests\Web\Actions\Blog\Category;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Actions\Blog\Category\GetCategoryByIdAction;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use JsonException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpBadRequestException;

class GetCategoryByIdActionTest extends TestCase
{
    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws JsonException
     * @throws HttpBadRequestException
     */
    public function testGetCategoryExists(): void
    {
        $cat = new BlogCategory();
        $cat->name = Uuid::uuid();
        $cat->create();

        $action = new GetCategoryByIdAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/category/' . $cat->getIdAsInt());
        $response = $action($request, new Response(), ['id' => $cat->getIdAsInt()]);
        $response->getBody()->rewind();

        $this->assertEquals(Action::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($cat->format(), json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws HttpBadRequestException
     */
    public function testGetCategoryNotExists(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Category not found');
        $cat = new BlogCategory();
        $cat->name = Uuid::uuid();
        $cat->create();

        $action = new GetCategoryByIdAction();
        $request = new ServerRequest(JinyaAction::GET, '/api/blog/category/-1');
        $action($request, new Response(), ['id' => -1]);
    }
}
