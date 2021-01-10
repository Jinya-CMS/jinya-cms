<?php

namespace App\Web\Actions\Frontend;

use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/', JinyaAction::GET)]
class GetHomeAction extends FrontAction
{
    /**
     * {@inheritDoc}
     */
    protected function protectedAction(): Response
    {
        return $this->render('theme::home', []);
    }
}
