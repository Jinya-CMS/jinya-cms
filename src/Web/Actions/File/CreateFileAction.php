<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateFileAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws ConflictException
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $file = new File();
        $file->name = $body['name'];
        try {
            $file->create();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Name already used');
        }


        return $this->respond($file->format(), Action::HTTP_CREATED);
    }
}