<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 06.06.18
 * Time: 18:53
 */

namespace Jinya\Services\Videos;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Video\Video;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Videos\VideoEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class VideoService implements VideoServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * VideoService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SlugServiceInterface $slugService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Video::class);
    }

    /**
     * Gets a list of videos in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Video[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        $this->eventDispatcher->dispatch(ListEvent::VIDEOS_PRE_GET_ALL, new ListEvent($offset, $count, $keyword, []));

        $videos = $this->createQueryBuilder($keyword)
            ->select('video')
            ->setMaxResults($count)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(ListEvent::VIDEOS_POST_GET_ALL, new ListEvent($offset, $count, $keyword, []));

        return $videos;
    }

    /**
     * @param string $keyword
     * @return QueryBuilder
     */
    private function createQueryBuilder(string $keyword): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Video::class, 'video')
            ->where('video.name LIKE :keyword')
            ->andWhere('video.description LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all videos filtered by the given keyword
     *
     * @param string $keyword
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $this->eventDispatcher->dispatch(CountEvent::VIDEOS_PRE_COUNT, new CountEvent($keyword, -1));

        $count = $this->createQueryBuilder($keyword)
            ->select('count(video)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(CountEvent::VIDEOS_POST_COUNT, new CountEvent($keyword, $count));

        return $count;
    }

    /**
     * Saves or update the given video
     *
     * @param Video $video
     * @return Video
     * @throws EmptySlugException
     */
    public function saveOrUpdate(Video $video): Video
    {
        $pre = $this->eventDispatcher->dispatch(VideoEvent::PRE_SAVE, new VideoEvent($video, $video->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($video);
            $this->eventDispatcher->dispatch(VideoEvent::POST_SAVE, new VideoEvent($video, $video->getSlug()));
        }

        return $video;
    }

    /**
     * Deletes the given video
     *
     * @param Video $video
     */
    public function delete(Video $video): void
    {
        $pre = $this->eventDispatcher->dispatch(VideoEvent::PRE_DELETE, new VideoEvent($video, $video->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->delete($video);
            $this->eventDispatcher->dispatch(VideoEvent::POST_DELETE, new VideoEvent($video, $video->getSlug()));
        }
    }

    /**
     * Gets the video by slug or id
     *
     * @param string $slug
     * @return Video
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function get(string $slug): ?Video
    {
        $this->eventDispatcher->dispatch(VideoEvent::PRE_GET, new VideoEvent(null, $slug));

        $video = $this->baseService->get($slug);
        $this->eventDispatcher->dispatch(VideoEvent::POST_GET, new VideoEvent($video, $slug));

        return $video;
    }
}
