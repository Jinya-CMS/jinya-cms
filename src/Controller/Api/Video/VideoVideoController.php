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
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoVideoController extends BaseApiController
{
    /**
     * @Route("/api/video/{slug}/video", name="api_video_get_video")
     *
     * @param string $slug
     * @param VideoServiceInterface $videoService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function getAction(string $slug, VideoServiceInterface $videoService, MediaServiceInterface $mediaService): Response
    {
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
}