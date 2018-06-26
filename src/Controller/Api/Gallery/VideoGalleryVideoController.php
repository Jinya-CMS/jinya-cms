<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:38
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Gallery\VideoGalleryFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use Jinya\Services\Videos\VideoPositionServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoGalleryVideoController extends BaseApiController
{
    /**
     * @Route("/api/gallery/video/{gallerySlug}/video", methods={"GET"}, name="api_gallery_video_video_get")
     *
     * @param string $gallerySlug
     * @param VideoGalleryServiceInterface $galleryService
     * @param VideoGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(string $gallerySlug, VideoGalleryServiceInterface $galleryService, VideoGalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($gallerySlug);

            return $galleryFormatter->init($gallery)->videos()->format()['videos'];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/video/{gallerySlug}/video", methods={"POST"}, name="api_gallery_video_video_position_post")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $gallerySlug
     * @param VideoPositionServiceInterface $videoPositionService
     * @return Response
     */
    public function postAction(string $gallerySlug, VideoPositionServiceInterface $videoPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $videoPositionService) {
            $position = $this->getValue('position', -1);
            $videoSlug = $this->getValue('video', null);

            if (empty($videoSlug)) {
                throw new MissingFieldsException(['video' => 'api.gallery.field.videoSlug.missing']);
            }

            return $videoPositionService->savePosition($gallerySlug, $videoSlug, $position);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/video/{gallerySlug}/video/{id}", methods={"DELETE"}, name="api_gallery_video_video_position_delete")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int id$
     * @param VideoPositionServiceInterface $videoPositionService
     * @return Response
     */
    public function deleteAction(int $id, VideoPositionServiceInterface $videoPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $videoPositionService) {
            $videoPositionService->deletePosition($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/video/{gallerySlug}/video/{id}/{oldPosition}", methods={"PUT"}, name="api_gallery_video_video_position_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $id
     * @param int $oldPosition
     * @param string $gallerySlug
     * @param VideoPositionServiceInterface $videoPositionService
     * @return Response
     */
    public function putPositionAction(int $id, int $oldPosition, string $gallerySlug, VideoPositionServiceInterface $videoPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $id, $oldPosition, $videoPositionService) {
            $newPosition = $this->getValue('position', null);
            $videoSlug = $this->getValue('video', null);

            if (!empty($videoSlug)) {
                $videoPositionService->updateVideo($id, $videoSlug);
            }
            if (isset($newPosition) && null !== $newPosition) {
                $videoPositionService->updatePosition($gallerySlug, $id, $oldPosition, $newPosition);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
