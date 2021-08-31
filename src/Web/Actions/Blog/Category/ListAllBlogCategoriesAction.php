<?php

namespace App\Web\Actions\Blog\Category;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/category', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class ListAllBlogCategoriesAction extends Action
{

    /**
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        if (isset($this->queryParams['keyword'])) {
            return $this->respondList($this->formatIterator(BlogCategory::findByKeyword($this->queryParams['keyword'])));
        }

        return $this->respondList($this->formatIterator(BlogCategory::findAll()));
    }
}