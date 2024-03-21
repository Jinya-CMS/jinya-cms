<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\UniqueFailedException;
use App\Storage\FileUploadService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

/**
 * Action to finalise a file upload
 */
class FinishUploadAction extends Action
{
    /** @var FileUploadService The file upload service */
    private FileUploadService $fileUploadService;

    /**
     * FinishUploadAction constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileUploadService = new FileUploadService();
    }

    /**
     * Finalises the upload for the given file
     *
     * @return Response
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        try {
            $this->fileUploadService->finishUpload($fileId);
        } catch (\Jinya\PDOx\Exceptions\NoResultException) {
            throw new HttpNotFoundException($this->request, 'File not found');
        }

        return $this->noContent();
    }
}
