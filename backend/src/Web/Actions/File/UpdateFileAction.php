<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/file/{id}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
class UpdateFileAction extends Action
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
        $id = $this->args['id'];
        $file = File::findById($id);
        if ($file === null) {
            throw new NoResultException($this->request, 'File not found');
        }

        if (isset($body['name'])) {
            $file->name = $body['name'];
        }

        try {
            $file->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}