<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/post/{id}', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::READER)]
class DeletePostAction extends Action
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
        $id = $this->args['id'];
        $post = BlogPost::findById($id);
        if ($post === null) {
            throw new NoResultException($this->request, 'Post not found');
        }

        $post->delete();

        return $this->noContent();
    }
}