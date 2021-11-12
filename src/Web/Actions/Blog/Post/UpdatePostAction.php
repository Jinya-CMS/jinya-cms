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
use App\Web\Exceptions\NoResultException;
use HipsterJazzbo\FireAndForget\FireAndForget;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/blog/post/{id}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['title', 'slug'])]
class UpdatePostAction extends Action
{
    use PostActionHelper;

    /**
     * @return Response
     * @throws ConflictException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $post = BlogPost::findById($this->args['id']);
        if ($post === null) {
            throw new NoResultException($this->request, 'Post not found');
        }

        if (isset($this->body['categoryId'])) {
            $post->categoryId = $this->body['categoryId'];
        }
        $category = $post->getCategory();
        if ($post->public === false && ($this->body['public'] ?? false) && $category !== null && $category->webhookEnabled && $category->webhookUrl !== null) {
            $host = $this->request->getHeader('Host')[0];
            $this->executeHook($post, $host);
        }

        if (isset($this->body['title'])) {
            $post->title = $this->body['title'];
        }
        if (isset($this->body['slug'])) {
            $post->slug = $this->body['slug'];
        }
        if (isset($this->body['public'])) {
            $post->public = $this->body['public'];
        }
        if (isset($this->body['headerImageId'])) {
            $post->headerImageId = $this->body['headerImageId'];
        }

        try {
            $post->update();
        } catch (UniqueFailedException) {
            if (isset($this->body['slug'])) {
                $postWithSlug = BlogPost::findBySlug($this->body['slug']);
            } else {
                $postWithSlug = null;
            }
            /** @psalm-suppress PossiblyNullArrayAccess */
            if ($postWithSlug !== null && $postWithSlug->id !== $this->args['id']) {
                throw new ConflictException($this->request, 'Slug exists');
            }

            throw new ConflictException($this->request, 'Title exists');
        }

        return $this->noContent();
    }
}