<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 06.06.18
 * Time: 21:27
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoPosterController extends BaseApiController
{
    /**
     * @Route("/api/video/jinya/{slug}/poster", name="api_video_get_poster", methods={"GET"})
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
            if (empty($video->getPoster())) {
                throw new FileNotFoundException($video->getName());
            }

            return $video;
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        } else {
            return $this->file($mediaService->getMedia($data->getPoster()), $data->getName() . '.poster.jpg');
        }
    }

    /**
     * @Route("/api/video/jinya/{slug}/poster", methods={"PUT"}, name="api_video_put_poster")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     * @param VideoServiceInterface $videoService
     * @param MediaServiceInterface $mediaService
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function putAction(
        string $slug,
        Request $request,
        VideoServiceInterface $videoService,
        MediaServiceInterface $mediaService,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        list($data, $status) = $this->tryExecute(function () use (
            $request,
            $videoService,
            $mediaService,
            $urlGenerator,
            $slug
        ) {
            $video = $videoService->get($slug);

            $poster = $request->getContent(true);
            $posterPath = $mediaService->saveMedia($poster, MediaServiceInterface::VIDEO_POSTER);

            if ($poster) {
                $video->setPoster($posterPath);
            }

            $videoService->saveOrUpdate($video);

            return $urlGenerator->generate(
                'api_video_get_video',
                ['slug' => $video->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }
}
