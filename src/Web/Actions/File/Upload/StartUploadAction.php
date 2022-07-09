<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\UploadingFile;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

/**
 * Action to initialize an upload
 */
class StartUploadAction extends Action
{

    /**
     * Starts a new file upload and creates an UploadingFile
     *
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
            throw new HttpNotFoundException($this->request, 'File not found');
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Upload started already');
        }

        return $this->noContent();
    }
}