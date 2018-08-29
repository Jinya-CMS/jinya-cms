<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Form\FormEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormCacheSubscriber implements EventSubscriberInterface
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
            FormEvent::POST_SAVE => 'onFormSave',
        ];
    }

    public function onFormSave(FormEvent $event)
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getSlug(), CacheBuilderInterface::FORM);
    }
}