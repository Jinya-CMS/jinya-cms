<?php

namespace App\Web\Actions\File;

use App\Database\File;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class GetFileByIdAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        $artist = File::findById((int)$this->args['id']);
        if ($artist === null) {
            throw new HttpNotFoundException($this->request, 'Artist not found');
        }

        return $this->respond($artist->format());
    }
}