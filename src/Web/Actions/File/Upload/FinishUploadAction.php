<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\FileUploadService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class FinishUploadAction extends Action
{
    private FileUploadService $fileUploadService;

    /**
     * FinishUploadAction constructor.
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
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        try {
            $this->fileUploadService->finishUpload($fileId);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ForeignKeyFailedException $exception) {
            throw new NoResultException($this->request, 'File not found');
        }

        return $this->noContent();
    }
}