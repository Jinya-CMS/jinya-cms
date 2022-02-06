<?php

namespace App\Web\Actions\Blog\Category;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/category/{id}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
class UpdateCategoryAction extends Action
{

    /**
     * @return Response
     * @throws ConflictException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $category = BlogCategory::findById($id);
        if ($category === null) {
            throw new NoResultException($this->request, 'Category not found');
        }

        if (isset($this->body['name'])) {
            $category->name = $this->body['name'];
        }
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
            $category->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name exists');
        }

        return $this->noContent();
    }
}