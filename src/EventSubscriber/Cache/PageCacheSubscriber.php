<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Pages\PageEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageCacheSubscriber implements EventSubscriberInterface
{
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
            PageEvent::POST_SAVE => 'onPageSave',
        ];
    }

    public function onPageSave(PageEvent $event): void
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getSlug(), CacheBuilderInterface::PAGE);
    }
}
