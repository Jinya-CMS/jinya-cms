<?php

namespace App\Web\Actions\File\Upload;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Storage\FileUploadService;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JetBrains\PhpStorm\Pure;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

#[JinyaAction('/api/media/file/{id}/content/finish', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
#[OpenApiRequest('This action finishes the file upload')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully finished the file upload', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('File not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'File not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class FinishUploadAction extends Action
{
    private FileUploadService $fileUploadService;

    /**
     * FinishUploadAction constructor.
     * @param LoggerInterface $logger
     * @param FileUploadService $fileUploadService
     */
    #[Pure] public function __construct(LoggerInterface $logger, FileUploadService $fileUploadService)
    {
        parent::__construct($logger);
        $this->fileUploadService = $fileUploadService;
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