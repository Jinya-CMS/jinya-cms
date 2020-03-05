<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.10.2017
 * Time: 23:52
 */

namespace Jinya\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectToInstallWizardEventSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private string $kernelProjectDir;

    /** @var UrlGeneratorInterface */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * RedirectToInstallWizardEventSubscriber constructor.
     * @param string $kernelProjectDir
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(string $kernelProjectDir, UrlGeneratorInterface $urlGenerator)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SymfonyKernelEvents::REQUEST => 'onSymfonyRequest',
        ];
    }

    public function onSymfonyRequest(RequestEvent $event): void
    {
        $installLock = $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'config/install.lock';
        $fs = new FileSystem();
        $installed = $fs->exists($installLock);

        $isInInstall = strpos($event->getRequest()->getPathInfo(), '/install') !== false;
        if (!$installed && $isInInstall) {
            if ($fs->exists(sprintf('%s/config/admin.lock', $this->kernelProjectDir))) {
                $event->setResponse(new RedirectResponse($this->urlGenerator->generate('install_admin')));
            } else {
                $event->setResponse(new RedirectResponse($this->urlGenerator->generate('install_index')));
            }
        } elseif ($installed && $isInInstall) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate(
                'designer_home_index_specific',
                ['route' => 'login']
            )));
        }
    }
}
