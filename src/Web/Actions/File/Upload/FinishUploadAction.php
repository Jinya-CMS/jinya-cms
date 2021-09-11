<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\FileUploadService;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/file/{id}/content/finish', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
class FinishUploadAction extends Action
{
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
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        try {
            $this->fileUploadService->finishUpload($fileId);
        } catch (ForeignKeyFailedException) {
            throw new NoResultException($this->request, 'File not found');
        }

        return $this->noContent();
    }
}