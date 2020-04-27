<?php

namespace App\Web\Actions\File\Upload;

use App\Storage\FileUploadService;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CleanChunksAction extends Action
{

    private FileUploadService $fileUploadService;

    /**
     * CleanChunksAction constructor.
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
     */
    protected function action(): Response
    {
        $this->fileUploadService->clearChunks($this->args['id']);

        return $this->noContent();
    }
}