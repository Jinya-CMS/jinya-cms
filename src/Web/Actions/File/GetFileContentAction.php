<?php

namespace App\Web\Actions\File;

use App\Database\File;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class GetFileContentAction extends Action
{

    /**
     * @inheritDoc
     * @throws HttpNotFoundException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        $file = File::findById($fileId);

        if ($file === null) {
            throw new HttpNotFoundException($this->request, 'File not found');
        }

        return $this->respondFile($file->path, $file->type);
    }
}