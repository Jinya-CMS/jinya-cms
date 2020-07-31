<?php

namespace App\Web\Actions\File;

use App\Database\File;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllFilesAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(File::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(File::findAll()));
    }
}