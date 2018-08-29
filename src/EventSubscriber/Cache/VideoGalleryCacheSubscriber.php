<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Galleries\ArtGalleryEvent;
use Jinya\Framework\Events\Galleries\VideoGalleryEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VideoGalleryCacheSubscriber implements EventSubscriberInterface
{
    /** @var CacheBuilderInterface */
    private $cacheBuilder;

    /**
     * ArtGalleryCacheSubscriber constructor.
     * @param CacheBuilderInterface $cacheBuilder
     */
    public function __construct(CacheBuilderInterface $cacheBuilder)
    {
        $this->cacheBuilder = $cacheBuilder;
    }

    public static function getSubscribedEvents()
    {
        return [
            VideoGalleryEvent::POST_SAVE => 'onVideoGallerySave',
        ];
    }

    public function onVideoGallerySave(ArtGalleryEvent $event)
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getSlug(), CacheBuilderInterface::VIDEO_GALLERY);
    }
}