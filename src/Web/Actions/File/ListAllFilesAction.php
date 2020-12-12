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
     * @return Response
     * @throws JsonException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
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