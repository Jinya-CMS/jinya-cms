<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 15.01.2018
 * Time: 22:32
 */

namespace Jinya\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class KernelExceptionSubscriber implements EventSubscriberInterface
{
    /** @var RouterInterface */
    private $router;

    /** @var LoggerInterface */
    private $logger;

    /**
     * KernelExceptionSubscriber constructor.
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(RouterInterface $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        if ('dev' !== getenv('APP_ENV')) {
            $request = $event->getRequest();
            $this->logger->warning($event->getException()->getMessage());
            $this->logger->warning($event->getException()->getTraceAsString());

            $route = $this->router->getRouteCollection()->get($request->get('_route'));

            if ('/{route}' === $route->getPath()) {
                $event->setResponse(new RedirectResponse('/'));
            }
        }
    }
}
