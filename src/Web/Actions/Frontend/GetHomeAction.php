<?php

namespace App\Web\Actions\Frontend;

use Psr\Http\Message\ResponseInterface as Response;

class GetHomeAction extends FrontAction
{

    /**
     * @inheritDoc
     */
    protected function protectedAction(): Response
    {
        return $this->render('theme::home', []);
    }
}