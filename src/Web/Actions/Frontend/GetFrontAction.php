<?php

namespace App\Web\Actions\Frontend;

use App\Database\MenuItem;
use Psr\Http\Message\ResponseInterface as Response;

class GetFrontAction extends FrontAction
{
    /**
     * @throws \App\Database\Exceptions\UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
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

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}
