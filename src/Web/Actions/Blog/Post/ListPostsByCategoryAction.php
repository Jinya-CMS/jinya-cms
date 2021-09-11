<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/category/{id}/post', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class ListPostsByCategoryAction extends Action
{
    /**
     * @return Response
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $category = BlogCategory::findById($this->args['id']);
        if ($category === null) {
            throw new NoResultException($this->request, 'Category not found');
        }

        return $this->respondList($this->formatIterator($category->getBlogPosts(true)));
    }
}