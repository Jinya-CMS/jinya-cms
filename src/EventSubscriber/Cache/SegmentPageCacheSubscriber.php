<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\SegmentPages\SegmentPageEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SegmentPageCacheSubscriber implements EventSubscriberInterface
{
    private CacheBuilderInterface $cacheBuilder;

    /**
     * ArtworkCacheSubscriber constructor.
     */
    public function __construct(CacheBuilderInterface $cacheBuilder)
    {
        $this->cacheBuilder = $cacheBuilder;
    }

    public static function getSubscribedEvents()
    {
        return [
            SegmentPageEvent::POST_SAVE => 'onSegmentPageSave',
        ];
    }

    public function onSegmentPageSave(SegmentPageEvent $event): void
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getSlug(), CacheBuilderInterface::SEGMENT_PAGE);
    }
}
