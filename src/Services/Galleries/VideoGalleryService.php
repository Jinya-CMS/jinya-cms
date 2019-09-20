<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.06.18
 * Time: 21:19
 */

namespace Jinya\Services\Galleries;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Galleries\VideoGalleryEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class VideoGalleryService implements VideoGalleryServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * VideoGalleryService constructor.
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
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, VideoGallery::class);
    }

    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return VideoGallery
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function get(string $slug): VideoGallery
    {
        $this->eventDispatcher->dispatch(new VideoGalleryEvent(null, $slug), VideoGalleryEvent::PRE_GET);

        $gallery = $this->baseService->get($slug);

        $this->eventDispatcher->dispatch(new VideoGalleryEvent($gallery, $slug), VideoGalleryEvent::POST_GET);

        return $gallery;
    }

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return VideoGallery[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        $this->eventDispatcher->dispatch(
            new ListEvent($keyword, []),
            ListEvent::VIDEO_GALLERIES_PRE_GET_ALL
        );

        $galleries = $this->createQueryBuilder($keyword)
            ->select('video_gallery')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(
            new ListEvent($keyword, $galleries),
            ListEvent::VIDEO_GALLERIES_POST_GET_ALL
        );

        return $galleries;
    }

    private function createQueryBuilder(string $keyword): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(VideoGallery::class, 'video_gallery')
            ->where('video_gallery.name LIKE :keyword')
            ->andWhere('video_gallery.description LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all galleries
     *
     * @param string $keyword
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::VIDEO_GALLERIES_PRE_COUNT);

        $count = $this->createQueryBuilder($keyword)
            ->select('count(video_gallery)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::VIDEO_GALLERIES_POST_COUNT);

        return $count;
    }

    /**
     * Saves or updates the given gallery
     *
     * @param VideoGallery $gallery
     * @return VideoGallery
     * @throws EmptySlugException
     */
    public function saveOrUpdate(VideoGallery $gallery): VideoGallery
    {
        $pre = $this->eventDispatcher->dispatch(
            new VideoGalleryEvent($gallery, $gallery->getSlug()),
            VideoGalleryEvent::PRE_SAVE
        );

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($gallery);
            $this->eventDispatcher->dispatch(
                new VideoGalleryEvent($gallery, $gallery->getSlug()),
                VideoGalleryEvent::POST_SAVE
            );
        }

        return $gallery;
    }

    /**
     * Deletes the given gallery
     *
     * @param VideoGallery $gallery
     */
    public function delete(VideoGallery $gallery): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new VideoGalleryEvent($gallery, $gallery->getSlug()),
            VideoGalleryEvent::PRE_DELETE
        );

        if (!$pre->isCancel()) {
            $this->baseService->delete($gallery);
            $this->eventDispatcher->dispatch(
                new VideoGalleryEvent($gallery, $gallery->getSlug()),
                VideoGalleryEvent::POST_DELETE
            );
        }
    }
}
