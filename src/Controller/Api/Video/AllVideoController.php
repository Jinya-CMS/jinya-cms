<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 27.06.18
 * Time: 09:12
 */

namespace Jinya\Controller\Api\Video;

use Jinya\Formatter\Video\VideoFormatterInterface;
use Jinya\Formatter\Video\YoutubeVideoFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Videos\AllVideoServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AllVideoController extends BaseApiController
{
    /**
     * @Route("/api/video/any", name="api_video_any_get_all", methods={"GET"})
     *
     * @param Request $request
     * @param AllVideoServiceInterface $allVideoService
     * @param VideoFormatterInterface $videoFormatter
     * @param YoutubeVideoFormatterInterface $youtubeVideoFormatter
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function getAllAction(Request $request, AllVideoServiceInterface $allVideoService, VideoFormatterInterface $videoFormatter, YoutubeVideoFormatterInterface $youtubeVideoFormatter, UrlGeneratorInterface $urlGenerator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $allVideoService, $videoFormatter, $youtubeVideoFormatter, $urlGenerator) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');

            $entities = array_map(function ($item) use ($urlGenerator) {
                if (array_key_exists('videoKey', $item)) {
                    return [
                        'type' => 'youtube',
                        'viewKey' => $item['videoKey'],
                        'slug' => $item['slug'],
                        'name' => $item['name']
                    ];
                } else if (array_key_exists('video', $item)) {
                    return [
                        'type' => 'jinya',
                        'poster' => $urlGenerator->generate('api_video_get_poster', ['slug' => $item['slug']]),
                        'video' => $urlGenerator->generate('api_video_get_video', ['slug' => $item['slug']]),
                        'slug' => $item['slug'],
                        'name' => $item['name']
                    ];
                }
            }, $allVideoService->getAll($offset, $count, $keyword));
            $totalCount = $allVideoService->countAll($keyword);

            return $this->formatListResult($totalCount, $offset, $count, ['keyword' => $keyword], 'api_video_get_all', $entities);
        });

        return $this->json($data, $status);
    }
}