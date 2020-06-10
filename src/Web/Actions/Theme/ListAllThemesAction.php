<?php

namespace App\Web\Actions\Theme;

use App\Database\Theme;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllThemesAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        return $this->respondList($this->formatIterator(Theme::findAll()));
    }
}