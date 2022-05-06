<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class CreatePostAction extends Action
{

    use PostActionHelper;

    /**
     * @return Response
     * @throws ConflictException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws NoResultException
     * @throws NoResultException
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
            $category = $post->getCategory();
            if ($post->public && $category !== null && $category->webhookEnabled && $category->webhookUrl !== null) {
                $host = $this->request->getHeader('Host')[0];
                $this->executeHook($post, $host);
            }
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