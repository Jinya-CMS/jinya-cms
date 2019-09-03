<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.06.18
 * Time: 16:41
 */

namespace Jinya\Controller\Api\Video;

use Jinya\Entity\Video\Video;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Video\VideoFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Videos\VideoServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends BaseApiController
{
    /**
     * @Route("/api/video/jinya", name="api_video_get_all", methods={"GET"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param Request $request
     * @param VideoServiceInterface $VideoService
     * @param VideoFormatterInterface $formatter
     * @return Response
     */
    public function getAllAction(
        Request $request,
        VideoServiceInterface $VideoService,
        VideoFormatterInterface $formatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($request, $formatter, $VideoService) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');
            $videos = $VideoService->getAll($offset, $count, $keyword);
            $allCount = $VideoService->countAll($keyword);

            $videos = array_map(static function (Video $video) use ($formatter) {
                return $formatter
                    ->init($video)
                    ->slug()
                    ->description()
                    ->name()
                    ->video()
                    ->poster()
                    ->created()
                    ->updated()
                    ->history()
                    ->format();
            }, $videos);

            return $this->formatListResult(
                $allCount,
                $offset,
                $count,
                ['keyword' => $keyword],
                'api_video_get_all',
                $videos
            );
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/jinya/{slug}", name="api_video_get", methods={"GET"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param VideoServiceInterface $VideoService
     * @param VideoFormatterInterface $formatter
     * @return Response
     */
    public function getAction(
        string $slug,
        VideoServiceInterface $VideoService,
        VideoFormatterInterface $formatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $formatter, $VideoService) {
            $video = $VideoService->get($slug);

            return [
                'success' => true,
                'item' => $formatter
                    ->init($video)
                    ->slug()
                    ->description()
                    ->name()
                    ->video()
                    ->poster()
                    ->created()
                    ->updated()
                    ->history()
                    ->format(),
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/jinya", name="api_video_post", methods={"POST"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param VideoServiceInterface $VideoService
     * @return Response
     */
    public function postAction(VideoServiceInterface $VideoService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($VideoService) {
            $slug = $this->getValue('slug', '');
            $name = $this->getValue('name');
            $description = $this->getValue('description');

            $missingFields = [];

            if (!$name) {
                $missingFields['name'] = 'api.label.field.name.missing';
            }

            if (!empty($missingFields)) {
                throw new MissingFieldsException($missingFields);
            }

            $video = new Video();
            $video->setSlug($slug);
            $video->setName($name);
            $video->setDescription($description);

            $VideoService->saveOrUpdate($video);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/jinya/{slug}", name="api_video_put", methods={"PUT"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param VideoServiceInterface $VideoService
     * @return Response
     */
    public function putAction(string $slug, VideoServiceInterface $VideoService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($slug, $VideoService) {
            $video = $VideoService->get($slug);

            $slug = $this->getValue('slug', $video->getSlug());
            $name = $this->getValue('name', $video->getName());
            $description = $this->getValue('description', $video->getDescription());

            $video->setSlug($slug);
            $video->setName($name);
            $video->setDescription($description);

            $VideoService->saveOrUpdate($video);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/video/jinya/{slug}", name="api_video_delete", methods={"DELETE"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param VideoServiceInterface $VideoService
     * @return Response
     */
    public function deleteAction(string $slug, VideoServiceInterface $VideoService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $VideoService) {
            $VideoService->delete($VideoService->get($slug));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
