<?php

namespace App\Web\Actions\Frontend;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Action to show blog frontend
 */
class GetBlogFrontAction extends FrontAction
{

    /**
     * Displays the given blog post or returns a 404 page
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws Throwable
     */
    protected function protectedAction(): Response
    {
        $slug = $this->args['slug'];
        $blogPost = BlogPost::findBySlug($slug);
        if ($blogPost !== null && $blogPost->public) {
            return $this->render('theme::blog-post', ['post' => $blogPost]);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}