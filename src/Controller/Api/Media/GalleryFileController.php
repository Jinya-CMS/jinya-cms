<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:38
 */

namespace Jinya\Controller\Api\Media;

use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Media\GalleryFilePositionFormatterInterface;
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
     * @Route("/api/media/gallery/file/{galleryId}/file", methods={"GET"}, name="api_gallery_file_get")
     *
     * @param int $galleryId
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(
        int $galleryId,
        GalleryServiceInterface $galleryService,
        GalleryFormatterInterface $galleryFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($galleryId, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($galleryId);

            return $galleryFormatter
                ->init($gallery)
                ->files()
                ->format()['files'];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/gallery/file/{galleryId}/file", methods={"POST"}, name="api_gallery_file_position_post")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $galleryId
     * @param GalleryFilePositionServiceInterface $filePositionService
     * @param GalleryFilePositionFormatterInterface $galleryFilePositionFormatter
     * @return Response
     */
    public function postAction(
        int $galleryId,
        GalleryFilePositionServiceInterface $filePositionService,
        GalleryFilePositionFormatterInterface $galleryFilePositionFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $galleryId,
            $filePositionService,
            $galleryFilePositionFormatter
        ) {
            $position = $this->getValue('position', -1);
            $fileId = $this->getValue('file');

            if (empty($fileId)) {
                throw new MissingFieldsException(['file' => 'api.gallery.field.fileId.missing']);
            }

            $positionId = $filePositionService->savePosition($fileId, $galleryId, $position);

            return $galleryFilePositionFormatter->init($filePositionService->getPosition($positionId))
                ->gallery()
                ->file()
                ->id()
                ->position()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route(
     *     "/api/media/gallery/file/{galleryId}/file/{id}",
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
     *     "/api/media/gallery/file/{galleryId}/file/{id}/{oldPosition}",
     *     methods={"PUT"},
     *     name="api_gallery_file_position_put"
     * )
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $id
     * @param int $oldPosition
     * @param int $galleryId
     * @param GalleryFilePositionServiceInterface $filePositionService
     * @return Response
     */
    public function putPositionAction(
        int $id,
        int $oldPosition,
        int $galleryId,
        GalleryFilePositionServiceInterface $filePositionService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $galleryId,
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
                $filePositionService->updatePosition($galleryId, $id, $oldPosition, $newPosition);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
