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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class VideoService implements VideoServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

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
        $this->eventDispatcher->dispatch(new ListEvent($offset, $count, $keyword, []), ListEvent::VIDEOS_PRE_GET_ALL);

        $videos = $this->createQueryBuilder($keyword)
            ->select('video')
            ->setMaxResults($count)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(new ListEvent($offset, $count, $keyword, []), ListEvent::VIDEOS_POST_GET_ALL);

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
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::VIDEOS_PRE_COUNT);

        $count = $this->createQueryBuilder($keyword)
            ->select('count(video)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::VIDEOS_POST_COUNT);

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
        $pre = $this->eventDispatcher->dispatch(new VideoEvent($video, $video->getSlug()), VideoEvent::PRE_SAVE);

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($video);
            $this->eventDispatcher->dispatch(new VideoEvent($video, $video->getSlug()), VideoEvent::POST_SAVE);
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
        $pre = $this->eventDispatcher->dispatch(new VideoEvent($video, $video->getSlug()), VideoEvent::PRE_DELETE);

        if (!$pre->isCancel()) {
            $this->baseService->delete($video);
            $this->eventDispatcher->dispatch(new VideoEvent($video, $video->getSlug()), VideoEvent::POST_DELETE);
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
    public function get(string $slug): Video
    {
        $this->eventDispatcher->dispatch(new VideoEvent(null, $slug), VideoEvent::PRE_GET);

        $video = $this->baseService->get($slug);
        $this->eventDispatcher->dispatch(new VideoEvent($video, $slug), VideoEvent::POST_GET);

        return $video;
    }
}
