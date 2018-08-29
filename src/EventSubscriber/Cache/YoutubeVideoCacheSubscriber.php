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
use Jinya\Framework\Events\Videos\YoutubeVideoEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YoutubeVideoCacheSubscriber implements EventSubscriberInterface
{
    /** @var CacheBuilderInterface */
    private $cacheBuilder;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * YoutubeVideoCacheSubscriber constructor.
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
            YoutubeVideoEvent::POST_SAVE => 'onYoutubeVideoSave',
        ];
    }

    public function onYoutubeVideoSave(YoutubeVideoEvent $event)
    {
        $galleries = $this->entityManager->createQueryBuilder()
            ->select('gallery.slug')
            ->from(VideoGallery::class, 'gallery')
            ->join('gallery.youtubeVideos', 'position')
            ->join('position.youtubeVideo', 'youtubeVideo')
            ->where('youtubeVideo.slug = :slug')
            ->setParameter('slug', $event->getSlug())
            ->getQuery()
            ->getScalarResult();

        foreach ($galleries as $gallery) {
            $this->cacheBuilder->buildCacheBySlugAndType($gallery['slug'], CacheBuilderInterface::VIDEO_GALLERY);
        }
    }
}