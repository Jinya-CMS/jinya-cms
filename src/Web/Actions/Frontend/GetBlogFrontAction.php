<?php

namespace App\Web\Actions\Frontend;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Attributes\JinyaAction;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

#[JinyaAction('/{year:\d\d\d\d}/{month:\d\d}/{day:\d\d}/{slug}', JinyaAction::GET)]
class GetBlogFrontAction extends FrontAction
{

    /**
     * @inheritDoc
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
        if (null !== $blogPost && $blogPost->public) {
            return $this->render('theme::blog-post', ['post' => $blogPost]);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}