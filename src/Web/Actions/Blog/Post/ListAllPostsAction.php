<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/post', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class ListAllPostsAction extends Action
{

    /**
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        if (isset($this->queryParams['keyword'])) {
            return $this->respondList($this->formatIterator(BlogPost::findByKeyword($this->queryParams['keyword'])));
        }

        return $this->respondList($this->formatIterator(BlogPost::findAll()));
    }
}