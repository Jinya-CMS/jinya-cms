<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/file', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class ListAllFilesAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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
