<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 09.12.18
 * Time: 19:13
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Configuration\ConfigurationEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationCacheEventSubscriber implements EventSubscriberInterface
{
    /** @var CacheBuilderInterface */
    private CacheBuilderInterface $cacheBuilder;

    /**
     * ThemeCacheSubscriber constructor.
     */
    public function __construct(CacheBuilderInterface $cacheBuilder)
    {
        $this->cacheBuilder = $cacheBuilder;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigurationEvent::POST_WRITE => 'onConfigurationWrite',
        ];
    }

    public function onConfigurationWrite(): void
    {
        $this->cacheBuilder->clearCache();
        $this->cacheBuilder->buildCache();
    }
}
