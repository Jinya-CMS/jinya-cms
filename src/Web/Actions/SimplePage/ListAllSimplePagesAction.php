<?php

namespace App\Web\Actions\SimplePage;

use App\Database\SimplePage;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllSimplePagesAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(SimplePage::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(SimplePage::findAll()));
    }
}