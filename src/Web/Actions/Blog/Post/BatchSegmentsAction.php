<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\TransactionFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/post/{id}/segment', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['segments'])]
class BatchSegmentsAction extends Action
{
    /**
     * @return Response
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws TransactionFailedException
     */
    protected function action(): Response
    {
        $post = BlogPost::findById($this->args['id']);
        if ($post === null) {
            throw new NoResultException($this->request, 'Post not found');
        }

        /**
         * @psalm-suppress PossiblyNullArrayAccess
         * @psalm-suppress PossiblyNullArgument
         */
        $post->batchReplaceSegments($this->body['segments']);

        return $this->noContent();
    }
}