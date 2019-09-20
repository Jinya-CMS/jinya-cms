<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Framework\Events\Videos\VideoPositionEvent;
use Jinya\Framework\Events\Videos\VideoPositionUpdateEvent;
use Jinya\Framework\Events\Videos\VideoPositionUpdateVideoEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VideoPositionCacheSubscriber implements EventSubscriberInterface
{
    /** @var CacheBuilderInterface */
    private $cacheBuilder;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * VideoCacheSubscriber constructor.
     * @param CacheBuilderInterface $cacheBuilder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(CacheBuilderInterface $cacheBuilder, EntityManagerInterface $entityManager)
    {
        $this->cacheBuilder = $cacheBuilder;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            VideoPositionEvent::POST_SAVE => 'onVideoPositionSave',
            VideoPositionEvent::POST_DELETE => 'onVideoPositionDelete',
            VideoPositionUpdateEvent::POST_UPDATE => 'onVideoPositionUpdate',
            VideoPositionUpdateVideoEvent::POST_UPDATE_VIDEO => 'onVideoPositionUpdateVideo',
        ];
    }

    public function onVideoPositionDelete(VideoPositionEvent $event): void
    {
        $videoPosition = $event->getVideoPosition();
        /* @noinspection NullPointerExceptionInspection */
        $this->cacheBuilder->buildCacheBySlugAndType(
            $videoPosition->getGallery(),
            CacheBuilderInterface::VIDEO_GALLERY
        );
    }

    public function onVideoPositionSave(VideoPositionEvent $event): void
    {
        $videoPosition = $event->getVideoPosition();
        /* @noinspection NullPointerExceptionInspection */
        $this->cacheBuilder->buildCacheBySlugAndType(
            $videoPosition->getGallery()->getSlug(),
            CacheBuilderInterface::VIDEO_GALLERY
        );
    }

    public function onVideoPositionUpdate(VideoPositionUpdateEvent $event): void
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getGallerySlug(), CacheBuilderInterface::VIDEO_GALLERY);
    }

    public function onVideoPositionUpdateVideo(VideoPositionUpdateVideoEvent $event): void
    {
        $galleries = $this->entityManager->createQueryBuilder()
            ->select('gallery.slug')
            ->from(VideoGallery::class, 'gallery')
            ->join('gallery.videos', 'position')
            ->where('position.id = :id')
            ->setParameter('id', $event->getPositionId())
            ->getQuery()
            ->getScalarResult();

        foreach ($galleries as $gallery) {
            $this->cacheBuilder->buildCacheBySlugAndType($gallery['slug'], CacheBuilderInterface::VIDEO_GALLERY);
        }
    }
}
