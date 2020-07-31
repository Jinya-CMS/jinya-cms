<?php

namespace App\Web\Actions\File;

use App\Database\File;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteFileAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $file = File::findById($id);
        if ($file === null) {
            throw new NoResultException($this->request, 'File not found');
        }
        $file->delete();

        return $this->noContent();
    }
}