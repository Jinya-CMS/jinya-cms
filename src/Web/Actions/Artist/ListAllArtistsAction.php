<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllArtistsAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(Artist::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Artist::findAll()));
    }
}