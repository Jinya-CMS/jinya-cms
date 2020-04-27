<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\FileUploadService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class UploadChunkAction extends Action
{
    private FileUploadService $fileUploadService;

    /**
     * UploadChunkAction constructor.
     * @param LoggerInterface $logger
     * @param FileUploadService $fileUploadService
     */
    public function __construct(LoggerInterface $logger, FileUploadService $fileUploadService)
    {
        parent::__construct($logger);
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        $position = $this->args['position'];

        try {
            $this->fileUploadService->saveChunk($fileId, $position, $this->request->getBody()->getContents());
        } catch (EmptyResultException $exception) {
            throw new NoResultException($this->request, 'File not found');
        }

        return $this->noContent();
   }
}