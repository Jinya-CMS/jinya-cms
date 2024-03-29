<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to list posts by category
 */
class ListPostsByCategoryAction extends Action
{
    /**
     * Gets all posts from the given category, always includes the posts from child categories
     *
     * @return Response
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $category = BlogCategory::findById($this->args['id']);
        if ($category === null) {
            throw new NoResultException($this->request, 'Category not found');
        }

        $posts = $category->getBlogPosts(true);
        return $this->respondList($this->formatIterator($posts));
    }
}