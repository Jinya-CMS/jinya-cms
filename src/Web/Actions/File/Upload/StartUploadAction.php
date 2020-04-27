<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\UploadingFile;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class StartUploadAction extends Action
{

    /**
     * @inheritDoc
     * @throws UniqueFailedException
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $id;
        try {
            $uploadingFile->create();
        } catch (ForeignKeyFailedException $exception) {
            throw new NoResultException($this->request, 'File not found');
        }

        return $this->noContent();
    }
}