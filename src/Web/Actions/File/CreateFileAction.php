<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/file', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['name'])]
class CreateFileAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $file = new File();
        $file->name = $body['name'];
        try {
            $file->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }


        return $this->respond($file->format(), Action::HTTP_CREATED);
    }
}