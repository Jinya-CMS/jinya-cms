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

#[JinyaAction('/api/blog/post/{id}', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class GetPostByIdAction extends Action
{

    /**
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $post = BlogPost::findById($id);
        if ($post === null) {
            throw new NoResultException($this->request, 'Post not found');
        }

        return $this->respond(payload: $post->format());
    }
}