<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 15.01.2018
 * Time: 22:32
 */

namespace HelperBundle\EventSubscriber;


use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Router;

class KernelExceptionSubscriber implements EventSubscriberInterface
{
    /** @var Router */
    private $router;
    /** @var Logger */
    private $logger;

    /**
     * KernelExceptionSubscriber constructor.
     * @param Router $router
     * @param Logger $logger
     */
    public function __construct(Router $router, Logger $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onException'
        ];
    }

    public function onException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $this->logger->warning($event->getException()->getMessage());
        $this->logger->warning($event->getException()->getTraceAsString());

        $route = $this->router->getRouteCollection()->get($request->get('_route'));
        if ($request->getPathInfo() === '/') {
        } elseif ($route->getPath() === '/{route}') {
            $event->setResponse(new RedirectResponse('/'));
        }
    }
}