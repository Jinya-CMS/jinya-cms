<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Database\UploadingFile;
use Jinya\Cms\Storage\FileUploadService;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Database\Exception\ForeignKeyFailedException;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Database\Exception\UniqueFailedException;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use PDOException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class FileController extends BaseController
{
    public function __construct(private readonly FileUploadService $fileUploadService = new FileUploadService())
    {
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/file/{id}/content')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getFileContent(int $id): ResponseInterface
    {
        $file = File::findById($id);
        if ($file === null) {
            return $this->entityNotFound('File not found');
        }

        return $this->file(StorageBaseService::PUBLIC_PATH . $file->path);
    }

    /**
     * @throws NotNullViolationException
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, 'api/file/{id}/content/{position:\d+}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function uploadChunk(int $id, int $position): ResponseInterface
    {
        try {
            $this->fileUploadService->saveChunk($id, $position, $this->request->getBody()->detach());
        } catch (EmptyResultException) {
            return $this->entityNotFound('File not found');
        }

        return $this->noContent();
    }

    /**
     * @throws NotNullViolationException
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, 'api/file/{id}/content/finish')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function finishUpload(int $id): ResponseInterface
    {
        try {
            $this->fileUploadService->finishUpload($id);
        } catch (EmptyResultException) {
            return $this->entityNotFound('File not found');
        }

        return $this->noContent();
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, 'api/file/{id}/content')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function startUpload(int $id): ResponseInterface
    {
        $uploadingFile = new UploadingFile();
        $uploadingFile->fileId = $id;
        try {
            $uploadingFile->create();
        } catch (UniqueFailedException) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'Upload was started previously',
                    'type' => 'uploaded-started',
                ],
            ], self::HTTP_CONFLICT);
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('File not found');
        } catch (PDOException $exception) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => $exception->getMessage(),
                    'type' => 'internal-server-error',
                ],
            ], self::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->noContent();
    }
}
