<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.10.2017
 * Time: 23:52
 */

namespace HelperBundle\EventSubscriber;

use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\Routing\Router;

class RequestEventSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $rootDir;
    /** @var Router */
    private $router;

    /**
     * RequestEventSubscriber constructor.
     * @param string $rootDir
     * @param Router $router
     */
    public function __construct($rootDir, Router $router)
    {
        $this->rootDir = $rootDir;
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
        if (strpos(strtolower($event->getRequest()->getPathInfo()), '/install') !== 0) {
            $fs = new FileSystem();
            try {
                $installLock = new File($this->rootDir . DIRECTORY_SEPARATOR . 'config/install.lock');
                if (!$fs->exists($installLock) && $installLock->getSize() > 0) {
                    $event->setResponse(new RedirectResponse($this->router->generate('install_default_index')));
                }
            } catch (Exception $ex) {
                $event->setResponse(new RedirectResponse($this->router->generate('install_default_index')));
            }
        }
    }
}