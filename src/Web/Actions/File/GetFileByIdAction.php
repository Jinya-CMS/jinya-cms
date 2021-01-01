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
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/media/file/{id}', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class GetFileByIdAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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