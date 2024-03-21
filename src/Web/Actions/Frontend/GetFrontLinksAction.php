<?php

namespace App\Web\Actions\Frontend;

use Psr\Http\Message\ResponseInterface as Response;

class GetFrontLinksAction extends FrontAction
{
    /**
     * @inheritDoc
     */
    protected function protectedAction(): Response
    {
        return $this->sendApiJson('api::links', []);
    }
}
