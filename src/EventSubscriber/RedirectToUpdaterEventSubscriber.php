<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 19.05.18
 * Time: 14:36
 */

namespace Jinya\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectToUpdaterEventSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private string $kernelProjectDir;

    /** @var UrlGeneratorInterface */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * RedirectToUpdaterEventSubscriber constructor.
     */
    public function __construct(string $kernelProjectDir, UrlGeneratorInterface $urlGenerator)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->urlGenerator = $urlGenerator;
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
            KernelEvents::REQUEST => 'onSymfonyRequest',
        ];
    }

    public function onSymfonyRequest(RequestEvent $event): void
    {
        $updateLock = $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'config/update.lock';
        $fs = new FileSystem();
        $updating = $fs->exists($updateLock);

        if ($updating) {
            $code = file_get_contents($updateLock);

            if ($code !== $event->getRequest()->cookies->get('JinyaUpdateKey')
                && false !== strpos($event->getRequest()->getPathInfo(), '/_update')) {
                $event->setResponse(new RedirectResponse($this->urlGenerator->generate('designer_home_index')));
            }
        }
    }
}
