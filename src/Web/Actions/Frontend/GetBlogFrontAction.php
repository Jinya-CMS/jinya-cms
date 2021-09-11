<?php

namespace App\Web\Actions\Frontend;

use App\Database\BlogPost;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/{year:\d\d\d\d}/{month:\d\d}/{day:\d\d}/{slug}', JinyaAction::GET)]
class GetBlogFrontAction extends FrontAction
{

    /**
     * @inheritDoc
     */
    protected function protectedAction(): Response
    {
        $slug = $this->args['slug'];
        $blogPost = BlogPost::findBySlug($slug);
        if (null !== $blogPost) {
            return $this->render('theme::blog-post', ['post' => $blogPost]);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}