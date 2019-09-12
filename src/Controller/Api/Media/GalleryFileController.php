<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:38
 */

namespace Jinya\Controller\Api\Media;

use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Media\GalleryFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\GalleryFilePositionServiceInterface;
use Jinya\Services\Media\GalleryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryFileController extends BaseApiController
{
    /**
     * @Route("/api/media/gallery/file/{gallerySlug}/file", methods={"GET"}, name="api_gallery_file_get")
     *
     * @param string $gallerySlug
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(
        string $gallerySlug,
        GalleryServiceInterface $galleryService,
        GalleryFormatterInterface $galleryFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($gallerySlug, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($gallerySlug);

            return $galleryFormatter
                ->init($gallery)
                ->files()
                ->format()['files'];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/gallery/file/{gallerySlug}/file", methods={"POST"}, name="api_gallery_file_position_post")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $gallerySlug
     * @param GalleryFilePositionServiceInterface $filePositionService
     * @return Response
     */
    public function postAction(string $gallerySlug, GalleryFilePositionServiceInterface $filePositionService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($gallerySlug, $filePositionService) {
            $position = $this->getValue('position', -1);
            $fileId = $this->getValue('file');

            if (empty($fileId)) {
                throw new MissingFieldsException(['file' => 'api.gallery.field.fileId.missing']);
            }

            return $filePositionService->savePosition($gallerySlug, $fileId, $position);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route(
     *     "/api/media/gallery/file/{gallerySlug}/file/{id}",
     *     methods={"DELETE"},
     *     name="api_gallery_file_position_delete"
     * )
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int id$
     * @param GalleryFilePositionServiceInterface $filePositionService
     * @return Response
     */
    public function deleteAction(int $id, GalleryFilePositionServiceInterface $filePositionService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $filePositionService) {
            $filePositionService->deletePosition($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route(
     *     "/api/media/gallery/file/{gallerySlug}/file/{id}/{oldPosition}",
     *     methods={"PUT"},
     *     name="api_gallery_file_position_put"
     * )
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $id
     * @param int $oldPosition
     * @param string $gallerySlug
     * @param GalleryFilePositionServiceInterface $filePositionService
     * @return Response
     */
    public function putPositionAction(
        int $id,
        int $oldPosition,
        string $gallerySlug,
        GalleryFilePositionServiceInterface $filePositionService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $gallerySlug,
            $id,
            $oldPosition,
            $filePositionService
        ) {
            $newPosition = $this->getValue('position');
            $fileSlug = $this->getValue('file');

            if (!empty($fileSlug)) {
                $filePositionService->updateFile($id, $fileSlug);
            }
            if (isset($newPosition) && null !== $newPosition) {
                $filePositionService->updatePosition($gallerySlug, $id, $oldPosition, $newPosition);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
