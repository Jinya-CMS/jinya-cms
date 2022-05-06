<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\UploadingFile;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class StartUploadAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $id;
        try {
            $uploadingFile->create();
        } catch (ForeignKeyFailedException) {
            throw new NoResultException($this->request, 'File not found');
        }

        return $this->noContent();
    }
}