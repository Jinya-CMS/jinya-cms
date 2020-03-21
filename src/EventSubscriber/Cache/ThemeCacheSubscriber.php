<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 20:43
 */

namespace Jinya\EventSubscriber\Cache;

use Jinya\Framework\Events\Theme\ThemeConfigEvent;
use Jinya\Framework\Events\Theme\ThemeLinkEvent;
use Jinya\Framework\Events\Theme\ThemeMenuEvent;
use Jinya\Framework\Events\Theme\ThemeVariablesEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

class ThemeCacheSubscriber implements EventSubscriberInterface
{
    /** @var ConfigurationServiceInterface */
    private ConfigurationServiceInterface $configService;

    /** @var CacheBuilderInterface */
    private CacheBuilderInterface $cacheBuilder;

    private LoggerInterface $logger;

    /**
     * ThemeCacheSubscriber constructor.
     * @param ConfigurationServiceInterface $configService
     * @param CacheBuilderInterface $cacheBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigurationServiceInterface $configService,
        CacheBuilderInterface $cacheBuilder,
        LoggerInterface $logger
    ) {
        $this->configService = $configService;
        $this->cacheBuilder = $cacheBuilder;
        $this->logger = $logger;
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
            ThemeLinkEvent::POST_SAVE => 'onThemeLinksChange',
        ];
    }

    public function onThemeConfigChange(ThemeConfigEvent $event): void
    {
        $this->compileCache($event->getThemeName());
    }

    private function compileCache(string $name): void
    {
        if ($this->configService->getConfig()->getCurrentTheme()->getName() === $name) {
            $this->cacheBuilder->clearCache();
            $this->cacheBuilder->buildCache();
        }
    }

    public function onThemeVariablesChange(ThemeVariablesEvent $event): void
    {
        $this->compileCache($event->getThemeName());
    }

    public function onThemeMenusChange(ThemeMenuEvent $event): void
    {
        $this->compileCache($event->getThemeName());
    }

    public function onThemeLinksChange(ThemeLinkEvent $event): void
    {
        try {
            $this->compileCache($event->getThemeName());
        } catch (Throwable $exception) {
            $this->logger->warning($exception);
        }
    }
}
