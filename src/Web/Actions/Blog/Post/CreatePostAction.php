<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/post', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['title', 'slug'])]
class CreatePostAction extends Action
{

    /**
     * @return Response
     * @throws ConflictException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $post = new BlogPost();
        /** @psalm-suppress PossiblyNullArrayAccess */
        $post->title = $this->body['title'];
        /** @psalm-suppress PossiblyNullArrayAccess */
        $post->slug = $this->body['slug'];
        $post->public = $this->body['public'] ?? false;
        if (isset($this->body['headerImageId'])) {
            $post->headerImageId = $this->body['headerImageId'];
        }
        if (isset($this->body['categoryId'])) {
            $post->categoryId = $this->body['categoryId'];
        }

        try {
            $post->create();
        } catch (UniqueFailedException) {
            /**
             * @psalm-suppress PossiblyNullArrayAccess
             * @psalm-suppress PossiblyNullArgument
             */
            if (BlogPost::findBySlug($this->body['slug']) !== null) {
                throw new ConflictException($this->request, 'Slug exists');
            }

            throw new ConflictException($this->request, 'Title exists');
        }

        return $this->respond($post->format(), Action::HTTP_CREATED);
    }
}