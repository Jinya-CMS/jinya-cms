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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoFileController extends BaseApiController
{
    /** @var string */
    private $tmp;

    /**
     * @param string $tmp
     */
    public function setTmp(string $tmp): void
    {
        $this->tmp = $tmp;
    }

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

    /**
     * @Route("/api/video/{slug}/video", methods={"PUT"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param Request $request
     * @param VideoServiceInterface $videoService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function startUploadAction(string $slug, Request $request, VideoServiceInterface $videoService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $request, $videoService, $mediaService) {
            $action = $request->get('action', 'chunk');
            $position = $request->get('position', 0);
            $data = $request->getContent(true);

            switch ($action) {
                case 'start':
                    $mediaService->saveMedia($data, $this->tmp . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 0);
                    break;
                case'chunk':
                    $mediaService->saveMedia($data, $this->tmp . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . $position);
                    break;
                case'end':
                    break;
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
