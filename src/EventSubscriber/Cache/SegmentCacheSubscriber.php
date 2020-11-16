<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\SegmentPages\SegmentEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SegmentCacheSubscriber implements EventSubscriberInterface
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
            SegmentEvent::POST_SAVE => 'onSegmentSave',
        ];
    }

    public function onSegmentSave(SegmentEvent $event): void
    {
        /* @noinspection NullPointerExceptionInspection */
        $this->cacheBuilder->buildCacheBySlugAndType(
            $event->getSegment()->getPage()->getSlug(),
            CacheBuilderInterface::SEGMENT_PAGE
        );
    }
}
