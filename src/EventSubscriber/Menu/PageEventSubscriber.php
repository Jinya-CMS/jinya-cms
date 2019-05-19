<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 30.08.18
 * Time: 10:12
 */

namespace Jinya\EventSubscriber\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Entity\Page\Page;
use Jinya\Framework\Events\Pages\PageEvent;
use Jinya\Framework\Events\Priority;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Underscore\Types\Strings;

class PageEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RoutingEntry[] */
    private $affectedRoutes;

    /**
     * PageEventSubscriber constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
            PageEvent::PRE_SAVE => 'onPrePageSave',
            PageEvent::POST_SAVE => ['onPostPageSave', Priority::CRITICAL],
        ];
    }

    public function onPostPageSave(PageEvent $event)
    {
        foreach ($this->affectedRoutes as $affectedRoute) {
            $parameter = $affectedRoute->getRouteParameter();
            $parameter['slug'] = $event->getSlug();
            $affectedRoute->setRouteParameter($parameter);
        }

        $this->entityManager->flush();
    }

    /**
     * @param PageEvent $event
     * @throws NonUniqueResultException
     */
    public function onPrePageSave(PageEvent $event)
    {
        $this->affectedRoutes = [];
        if (!empty($event->getPage()->getId())) {
            $oldSlug = $this->entityManager
                ->createQueryBuilder()
                ->select('page.slug')
                ->from(Page::class, 'page')
                ->where('page.id = :id')
                ->setParameter('id', $event->getPage()->getId())
                ->getQuery()
                ->getSingleScalarResult();

            $routes = $this->entityManager
                ->getRepository(RoutingEntry::class)
                ->findBy(['routeName' => 'frontend_page_details']);
            $this->affectedRoutes = array_filter($routes, function (RoutingEntry $routingEntry) use ($oldSlug) {
                $parameter = $routingEntry->getRouteParameter();

                return array_key_exists(
                    'slug',
                    $parameter
                ) && Strings::lower($parameter['slug']) === Strings::lower($oldSlug);
            });
        }
    }
}
