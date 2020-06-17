<?php

namespace App\Web\Actions\Frontend;

use App\Database\MenuItem;
use Psr\Http\Message\ResponseInterface as Response;

class GetFrontAction extends FrontAction
{

    protected function protectedAction(): Response
    {
        $route = $this->args['route'];
        if ($route === '' || $route === '/') {
            return $this->render('theme::home', []);
        }

        $menuItem = MenuItem::findByRoute($route);
        if ($menuItem !== null) {
            return $this->renderMenuItem($menuItem);
        }

        return $this->render('theme::not-found', [], self::HTTP_NOT_FOUND);
    }
}