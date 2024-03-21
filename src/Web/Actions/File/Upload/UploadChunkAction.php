<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\FileUploadService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to upload a chunk
 */
class UploadChunkAction extends Action
{
    /** @var FileUploadService The file upload service */
    private FileUploadService $fileUploadService;

    /**
     * UploadChunkAction constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileUploadService = new FileUploadService();
    }

    /**
     * Creates a new UploadingFileChunk for the given file and the given position
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        $position = $this->args['position'];

        try {
            $this->fileUploadService->saveChunk($fileId, $position, $this->request->getBody()->detach());
        } catch (EmptyResultException) {
            throw new NoResultException($this->request, 'File not found');
        }

        return $this->noContent();
    }
}
