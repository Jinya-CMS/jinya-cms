<?php

namespace App\Web\Actions\Frontend;

use App\Database\BlogPost;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Action to get the frontend
 */
class GetFrontAction extends FrontAction
{
    /**
     * Renders the given frontend route or a matching blog post if there is no menu item with the given route
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
        $route = $this->args['route'] ?? '';
        if ($route === '' || $route === '/') {
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::home', []);
            }

            return $this->render('theme::home', []);
        }

        $menuItem = MenuItem::findByRoute($route);
        if ($menuItem !== null) {
            return $this->renderMenuItem($menuItem);
        }

        $blogPost = BlogPost::findBySlug($route);
        if ($blogPost !== null && $blogPost->public) {
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::blog-post', ['post' => $blogPost]);
            }

            return $this->render('theme::blog-post', ['post' => $blogPost]);
        }

        if ($this->checkForApiRequest()) {
            return $this->sendApiJson('api::404', [], self::HTTP_NOT_FOUND);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}
