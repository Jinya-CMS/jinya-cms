<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.06.18
 * Time: 21:19
 */

namespace Jinya\Services\Galleries;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Galleries\VideoGalleryEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, VideoGallery::class);
    }

    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return VideoGallery
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $slug): VideoGallery
    {
        $this->eventDispatcher->dispatch(VideoGalleryEvent::PRE_GET, new VideoGalleryEvent(null, $slug));

        $gallery = $this->baseService->get($slug);

        $this->eventDispatcher->dispatch(VideoGalleryEvent::POST_GET, new VideoGalleryEvent($gallery, $slug));

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
        $this->eventDispatcher->dispatch(ListEvent::VIDEO_GALLERIES_PRE_GET_ALL, new ListEvent($offset, $count, $keyword, []));

        $galleries = $this->createQueryBuilder($keyword)
            ->select('video_gallery')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(ListEvent::VIDEO_GALLERIES_POST_GET_ALL, new ListEvent($offset, $count, $keyword, $galleries));

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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $this->eventDispatcher->dispatch(CountEvent::VIDEO_GALLERIES_PRE_COUNT, new CountEvent($keyword, -1));

        $count = $this->createQueryBuilder($keyword)
            ->select('count(video_gallery)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(CountEvent::VIDEO_GALLERIES_POST_COUNT, new CountEvent($keyword, $count));

        return $count;
    }

    /**
     * Saves or updates the given gallery
     *
     * @param VideoGallery $gallery
     * @return VideoGallery
     * @throws \Jinya\Exceptions\EmptySlugException
     */
    public function saveOrUpdate(VideoGallery $gallery): VideoGallery
    {
        $pre = $this->eventDispatcher->dispatch(VideoGalleryEvent::PRE_SAVE, new VideoGalleryEvent($gallery, $gallery->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($gallery);
            $this->eventDispatcher->dispatch(VideoGalleryEvent::POST_SAVE, new VideoGalleryEvent($gallery, $gallery->getSlug()));
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
        $pre = $this->eventDispatcher->dispatch(VideoGalleryEvent::PRE_DELETE, new VideoGalleryEvent($gallery, $gallery->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->delete($gallery);
            $this->eventDispatcher->dispatch(VideoGalleryEvent::POST_DELETE, new VideoGalleryEvent($gallery, $gallery->getSlug()));
        }
    }
}
