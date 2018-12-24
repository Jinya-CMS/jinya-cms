<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 20:43
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Theme\ThemeConfigEvent;
use Jinya\Framework\Events\Theme\ThemeMenuEvent;
use Jinya\Framework\Events\Theme\ThemeVariablesEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ThemeCacheSubscriber implements EventSubscriberInterface
{
    /** @var ConfigurationServiceInterface */
    private $configService;

    /** @var CacheBuilderInterface */
    private $cacheBuilder;

    /**
     * ThemeCacheSubscriber constructor.
     * @param ConfigurationServiceInterface $configService
     * @param CacheBuilderInterface $cacheBuilder
     */
    public function __construct(ConfigurationServiceInterface $configService, CacheBuilderInterface $cacheBuilder)
    {
        $this->configService = $configService;
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
            ThemeConfigEvent::POST_SAVE => 'onThemeConfigChange',
            ThemeConfigEvent::POST_RESET => 'onThemeConfigChange',
            ThemeVariablesEvent::POST_SAVE => 'onThemeVariablesChange',
            ThemeVariablesEvent::POST_RESET => 'onThemeVariablesChange',
            ThemeMenuEvent::POST_SAVE => 'onThemeMenusChange',
        ];
    }

    public function onThemeConfigChange(ThemeConfigEvent $event)
    {
        $this->compileCache($event->getThemeName());
    }

    private function compileCache(string $name)
    {
        if ($this->configService->getConfig()->getCurrentTheme()->getName() === $name) {
            $this->cacheBuilder->clearCache();
            $this->cacheBuilder->buildCache();
        }
    }

    public function onThemeVariablesChange(ThemeVariablesEvent $event)
    {
        $this->compileCache($event->getThemeName());
    }

    public function onThemeMenusChange(ThemeMenuEvent $event)
    {
        $this->compileCache($event->getThemeName());
    }
}
