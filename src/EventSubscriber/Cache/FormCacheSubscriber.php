<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Form\FormEvent;
use Jinya\Framework\Events\Form\FormItemEvent;
use Jinya\Framework\Events\Form\FormItemPositionEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormCacheSubscriber implements EventSubscriberInterface
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
            FormEvent::POST_SAVE => 'onFormSave',
            FormItemEvent::POST_ADD => 'onItemSave',
            FormItemEvent::POST_DELETE => 'onItemSave',
            FormItemEvent::POST_UPDATE => 'onItemSave',
            FormItemPositionEvent::POST_UPDATE => 'onItemPositionSave',
        ];
    }

    public function onFormSave(FormEvent $event): FormEvent
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getSlug(), CacheBuilderInterface::FORM);

        return $event;
    }

    public function onItemPositionSave(FormItemPositionEvent $event): FormItemPositionEvent
    {
        $form = $event->getForm();
        $this->cacheBuilder->buildCacheBySlugAndType($form->getSlug(), CacheBuilderInterface::FORM);

        return $event;
    }

    public function onItemSave(FormItemEvent $event): FormItemEvent
    {
        /** @noinspection NullPointerExceptionInspection */
        $form = $event->getFormItem()->getForm();
        $this->cacheBuilder->buildCacheBySlugAndType($form->getSlug(), CacheBuilderInterface::FORM);

        return $event;
    }
}
