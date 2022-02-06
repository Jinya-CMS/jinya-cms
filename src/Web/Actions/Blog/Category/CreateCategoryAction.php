<?php

namespace App\Web\Actions\Blog\Category;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/category', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['name'])]
class CreateCategoryAction extends Action
{

    /**
     * @return Response
     * @throws ConflictException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $category = new BlogCategory();
        /** @psalm-suppress PossiblyNullArrayAccess */
        $category->name = $this->body['name'];
        if (isset($this->body['description'])) {
            $category->description = $this->body['description'];
        }
        if (isset($this->body['parentId'])) {
            $category->parentId = $this->body['parentId'];
        }
        if (isset($this->body['webhookEnabled'])) {
            $category->webhookEnabled = $this->body['webhookEnabled'];
        }
        if (isset($this->body['webhookUrl'])) {
            $category->webhookUrl = $this->body['webhookUrl'];
        }

        try {
            $category->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name exists');
        }

        return $this->respond($category->format(), Action::HTTP_CREATED);
    }
}