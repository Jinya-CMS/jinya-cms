<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 06.06.18
 * Time: 21:29
 */

namespace Jinya\Controller\Api\Video;

use Jinya\Entity\Video\Video;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Videos\VideoServiceInterface;
use Jinya\Services\Videos\VideoUploadServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoFileController extends BaseApiController
{
    /**
     * @Route("/api/video/jinya/{slug}/video", name="api_video_get_video", methods={"GET"})
     *
     * @param string $slug
     * @param VideoServiceInterface $videoService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function getAction(
        string $slug,
        VideoServiceInterface $videoService,
        MediaServiceInterface $mediaService
    ): Response {
        /** @var $data Video|array */
        list($data, $status) = $this->tryExecute(function () use ($videoService, $slug) {
            $video = $videoService->get($slug);
            if (empty($video->getVideo())) {
                throw new FileNotFoundException($video->getName());
            }

            return $video;
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        } else {
            return $this->file($mediaService->getMedia($data->getVideo()), $data->getName() . '.mp4');
        }
    }

    /**
     * @Route("/api/video/jinya/{slug}/video", methods={"POST"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param VideoUploadServiceInterface $videoUploadService
     * @return Response
     */
    public function startUploadAction(string $slug, VideoUploadServiceInterface $videoUploadService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $videoUploadService) {
            $videoUploadService->startUpload($slug);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/jinya/{slug}/video/{position}", methods={"PUT"}, requirements={"position": "^\d*$"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param int $position
     * @param Request $request
     * @param VideoUploadServiceInterface $videoUploadService
     * @return Response
     */
    public function uploadChunkAction(
        string $slug,
        int $position,
        Request $request,
        VideoUploadServiceInterface $videoUploadService
    ): Response {
        list($data, $status) = $this->tryExecute(function () use ($slug, $position, $request, $videoUploadService) {
            $data = $request->getContent(true);

            $videoUploadService->uploadChunk($data, $position, $slug);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/jinya/{slug}/video/state", methods={"DELETE"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param VideoUploadServiceInterface $videoUploadService
     * @return Response
     */
    public function resetStateAction(string $slug, VideoUploadServiceInterface $videoUploadService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $videoUploadService) {
            $videoUploadService->cleanupAfterUpload($slug);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/jinya/{slug}/video/finish", methods={"PUT"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param VideoServiceInterface $videoService
     * @param VideoUploadServiceInterface $videoUploadService
     * @return Response
     */
    public function finishUploadAction(
        string $slug,
        VideoServiceInterface $videoService,
        VideoUploadServiceInterface $videoUploadService
    ): Response {
        list($data, $status) = $this->tryExecute(function () use ($slug, $videoService, $videoUploadService) {
            $video = $videoService->get($slug);
            $path = $videoUploadService->finishUpload($slug);

            $video->setVideo($path);

            $videoService->saveOrUpdate($video);

            $videoUploadService->cleanupAfterUpload($slug);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
