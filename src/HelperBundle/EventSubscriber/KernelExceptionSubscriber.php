<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 15.01.2018
 * Time: 22:32
 */

namespace HelperBundle\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Router;

class KernelExceptionSubscriber implements EventSubscriberInterface
{
    /** @var Router */
    private $router;

    /**
     * KernelExceptionSubscriber constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
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

        $route = $this->router->getRouteCollection()->get($request->get('_route'));
        if ($request->getPathInfo() === '/') {
        } elseif ($route->getPath() === '/{route}') {
            $event->setResponse(new RedirectResponse('/'));
        }
    }
}