<?php

namespace App\Web\Actions\Frontend;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

class GetFrontAction extends FrontAction
{
    /**
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws NoResultException
     */
    protected function protectedAction(): Response
    {
        $route = $this->args['route'];
        if ('' === $route || '/' === $route) {
            return $this->render('theme::home', []);
        }

        $menuItem = MenuItem::findByRoute($route);
        if (null !== $menuItem) {
            return $this->renderMenuItem($menuItem);
        }

        $blogPost = BlogPost::findBySlug($route);
        if (null !== $blogPost && $blogPost->public) {
            return $this->render('theme::blog-post', ['post' => $blogPost]);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}
