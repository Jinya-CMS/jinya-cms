<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

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
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
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