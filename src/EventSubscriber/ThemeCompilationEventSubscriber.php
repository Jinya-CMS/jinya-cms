<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 16.02.2018
 * Time: 17:37
 */

namespace Jinya\EventSubscriber;

use Exception;
use Jinya\Services\Theme\ThemeSyncServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Underscore\Types\Strings;

class ThemeCompilationEventSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ThemeSyncServiceInterface */
    private $themeSyncService;

    /**
     * ThemeCompilationEventSubscriber constructor.
     * @param LoggerInterface $logger
     * @param ThemeSyncServiceInterface $themeSyncService
     */
    public function __construct(LoggerInterface $logger, ThemeSyncServiceInterface $themeSyncService)
    {
        $this->logger = $logger;
        $this->themeSyncService = $themeSyncService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    public function onRequest(GetResponseEvent $event)
    {
        if (Strings::startsWith($event->getRequest()->getBasePath(), '/designer')) {
            try {
                $this->themeSyncService->syncThemes();
            } catch (Exception $e) {
                $this->logger->error('Error updating themes');
                $this->logger->error($e->getMessage());
                $this->logger->error($e->getTraceAsString());
            }
        }
    }
}
