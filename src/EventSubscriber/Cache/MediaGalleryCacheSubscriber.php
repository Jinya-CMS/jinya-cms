<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Media\GalleryEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MediaGalleryCacheSubscriber implements EventSubscriberInterface
{
    /** @var CacheBuilderInterface */
    private CacheBuilderInterface $cacheBuilder;

    /**
     * ArtGalleryCacheSubscriber constructor.
     */
    public function __construct(CacheBuilderInterface $cacheBuilder)
    {
        $this->cacheBuilder = $cacheBuilder;
    }

    public static function getSubscribedEvents()
    {
        return [
            GalleryEvent::POST_SAVE => 'onGallerySave',
        ];
    }

    public function onGallerySave(GalleryEvent $event): void
    {
        $this->cacheBuilder->buildCacheBySlugAndType(
            $event->getGallery()->getSlug(),
            CacheBuilderInterface::MEDIA_GALLERY
        );
    }
}
