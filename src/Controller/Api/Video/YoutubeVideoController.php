<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.06.18
 * Time: 16:41
 */

namespace Jinya\Controller\Api\Video;

use Jinya\Entity\Video\YoutubeVideo;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Video\YoutubeVideoFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Videos\YoutubeVideoServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YoutubeVideoController extends BaseApiController
{
    /**
     * @Route("/api/video/youtube", name="api_video_youtube_get_all", methods={"GET"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param Request $request
     * @param YoutubeVideoServiceInterface $youtubeVideoService
     * @param YoutubeVideoFormatterInterface $formatter
     * @return Response
     */
    public function getAllAction(Request $request, YoutubeVideoServiceInterface $youtubeVideoService, YoutubeVideoFormatterInterface $formatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $formatter, $youtubeVideoService) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');
            $videos = $youtubeVideoService->getAll($offset, $count, $keyword);
            $allCount = $youtubeVideoService->countAll($keyword);

            $videos = array_map(function (YoutubeVideo $video) use ($formatter) {
                return $formatter
                    ->init($video)
                    ->slug()
                    ->description()
                    ->name()
                    ->videoKey()
                    ->created()
                    ->updated()
                    ->history()
                    ->format();
            }, $videos);

            return $this->formatListResult($allCount, $offset, $count, [], 'api_video_youtube_get_all', $videos);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/youtube/{slug}", name="api_video_youtube_get", methods={"GET"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param YoutubeVideoServiceInterface $youtubeVideoService
     * @param YoutubeVideoFormatterInterface $formatter
     * @return Response
     */
    public function getAction(string $slug, YoutubeVideoServiceInterface $youtubeVideoService, YoutubeVideoFormatterInterface $formatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $formatter, $youtubeVideoService) {
            $video = $youtubeVideoService->get($slug);

            return [
                'success' => true,
                'item' => $formatter
                    ->init($video)
                    ->slug()
                    ->description()
                    ->name()
                    ->videoKey()
                    ->created()
                    ->updated()
                    ->history()
                    ->format()
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/youtube", name="api_video_youtube_post", methods={"POST"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param YoutubeVideoServiceInterface $youtubeVideoService
     * @return Response
     */
    public function postAction(YoutubeVideoServiceInterface $youtubeVideoService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($youtubeVideoService) {
            $slug = $this->getValue('slug');
            $name = $this->getValue('name');
            $description = $this->getValue('description');
            $videoKey = $this->getValue('videoKey');

            $missingFields = [];

            if (!$name) {
                $missingFields['name'] = 'api.label.field.name.missing';
            }
            if (!$videoKey) {
                $missingFields['videoKey'] = 'api.label.field.video_key.missing';
            }

            if (!empty($missingFields)) {
                throw new MissingFieldsException($missingFields);
            }

            $video = new YoutubeVideo();
            $video->setSlug($slug);
            $video->setName($name);
            $video->setDescription($description);
            $video->setVideoKey($videoKey);

            $youtubeVideoService->saveOrUpdate($video);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/youtube/{slug}", name="api_video_youtube_put", methods={"PUT"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param YoutubeVideoServiceInterface $youtubeVideoService
     * @return Response
     */
    public function putAction(string $slug, YoutubeVideoServiceInterface $youtubeVideoService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $youtubeVideoService) {
            $video = $youtubeVideoService->get($slug);

            $slug = $this->getValue('slug', $video->getSlug());
            $name = $this->getValue('name', $video->getName());
            $description = $this->getValue('description', $video->getDescription());
            $videoKey = $this->getValue('videoKey', $video->getVideoKey());

            $video->setSlug($slug);
            $video->setName($name);
            $video->setDescription($description);
            $video->setVideoKey($videoKey);

            $youtubeVideoService->saveOrUpdate($video);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/youtube/{slug}", name="api_video_youtube_delete", methods={"DELETE"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param YoutubeVideoServiceInterface $youtubeVideoService
     * @return Response
     */
    public function deleteAction(string $slug, YoutubeVideoServiceInterface $youtubeVideoService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $youtubeVideoService) {
            $youtubeVideoService->delete($youtubeVideoService->get($slug));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
