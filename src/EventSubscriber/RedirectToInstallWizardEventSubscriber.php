<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.10.2017
 * Time: 23:52
 */

namespace Jinya\EventSubscriber;

use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\Routing\RouterInterface;

class RedirectToInstallWizardEventSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $kernelProjectDir;
    /** @var RouterInterface */
    private $router;

    /**
     * RequestEventSubscriber constructor.
     * @param string $kernelProjectDir
     * @param RouterInterface $router
     */
    public function __construct(string $kernelProjectDir, RouterInterface $router)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SymfonyKernelEvents::REQUEST => 'onSymfonyRequest'
        ];
    }

    public function onSymfonyRequest(GetResponseEvent $event)
    {
        $installLock = $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'config/install.lock';
        $fs = new FileSystem();
        $installed = $fs->exists($installLock);

        if (stripos($event->getRequest()->getPathInfo(), '/install') !== 0) {
            try {
                if (!$installed) {
                    $event->setResponse(new RedirectResponse($this->router->generate('install_default_index')));
                }
            } catch (Exception $ex) {
                $event->setResponse(new RedirectResponse($this->router->generate('install_default_index')));
            }
        } else {
            if ($installed) {
                $event->setResponse(new RedirectResponse($this->router->generate('designer_default_index')));
            }
        }
    }
}