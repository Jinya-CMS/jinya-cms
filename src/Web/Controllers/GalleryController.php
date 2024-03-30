<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\File;
use Jinya\Cms\Database\Gallery;
use Jinya\Cms\Database\GalleryFilePosition;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class GalleryController extends BaseController
{
    /**
     * Gets all positions for the given gallery
     * @throws JsonException
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/gallery/{galleryId}/file')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getPositions(int $galleryId): ResponseInterface
    {
        $gallery = Gallery::findById($galleryId);
        if (!$gallery) {
            return $this->entityNotFound('Gallery not found');
        }

        return $this->jsonIteratorPlain($gallery->getFiles());
    }

    /**
     * Creates a new gallery file position for the given gallery
     *
     * @param int $galleryId
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::POST, 'api/gallery/{galleryId}/file')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['position', 'file']))]
    public function createPosition(int $galleryId): ResponseInterface
    {
        $position = $this->body['position'];
        $file = $this->body['file'];

        if (!Gallery::findById($galleryId)) {
            return $this->entityNotFound('Gallery not found');
        }

        if (!File::findById($file)) {
            return $this->entityNotFound('File not found');
        }

        $galleryFilePosition = new GalleryFilePosition();
        $galleryFilePosition->fileId = $file;
        $galleryFilePosition->galleryId = $galleryId;
        $galleryFilePosition->position = $position;

        $galleryFilePosition->create();

        return $this->json($galleryFilePosition->format(), self::HTTP_CREATED);
    }

    /**
     * Deletes the gallery file position by gallery id and position
     *
     * @param int $galleryId
     * @param int $position
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::DELETE, 'api/gallery/{galleryId}/file/{position}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function deletePosition(int $galleryId, int $position): ResponseInterface
    {
        $galleryFilePosition = GalleryFilePosition::findByPosition($galleryId, $position);
        if (!$galleryFilePosition) {
            return $this->entityNotFound('Gallery file position not found');
        }

        $galleryFilePosition->delete();

        return $this->noContent();
    }

    /**
     * Updates the given gallery file position by gallery id and position
     *
     * @param int $galleryId
     * @param int $position
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::PUT, 'api/gallery/{galleryId}/file/{position}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function updatePosition(int $galleryId, int $position): ResponseInterface
    {
        $galleryFilePosition = GalleryFilePosition::findByPosition($galleryId, $position);

        if (!$galleryFilePosition) {
            return $this->entityNotFound('Gallery file position not found');
        }


        $fileId = $this->body['file'] ?? null;

        if ($fileId) {
            $file = File::findById($fileId);
            if (!$file) {
                return $this->entityNotFound('File not found');
            }

            $galleryFilePosition->fileId = $fileId;
        }

        if (isset($this->body['newPosition'])) {
            $galleryFilePosition->move($this->body['newPosition']);
        }

        return $this->noContent();
    }
}
