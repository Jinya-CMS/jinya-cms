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
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Framework\Events\Artworks\ArtworkEvent;
use Jinya\Framework\Events\Priority;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Underscore\Types\Strings;

class ArtworkEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RoutingEntry[] */
    private $affectedRoutes;

    /**
     * ArtworkEventSubscriber constructor.
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
            ArtworkEvent::PRE_SAVE => 'onPreArtworkSave',
            ArtworkEvent::POST_SAVE => ['onPostArtworkSave', Priority::CRITICAL],
        ];
    }

    public function onPostArtworkSave(ArtworkEvent $event)
    {
        foreach ($this->affectedRoutes as $affectedRoute) {
            $parameter = $affectedRoute->getRouteParameter();
            $parameter['slug'] = $event->getSlug();
            $affectedRoute->setRouteParameter($parameter);
        }

        $this->entityManager->flush();
    }

    /**
     * @param ArtworkEvent $event
     * @throws NonUniqueResultException
     */
    public function onPreArtworkSave(ArtworkEvent $event)
    {
        $this->affectedRoutes = [];
        if (!empty($event->getArtwork()->getId())) {
            $oldSlug = $this->entityManager
                ->createQueryBuilder()
                ->select('artwork.slug')
                ->from(Artwork::class, 'artwork')
                ->where('artwork.id = :id')
                ->setParameter('id', $event->getArtwork()->getId())
                ->getQuery()
                ->getSingleScalarResult();

            $routes = $this->entityManager->getRepository(RoutingEntry::class)->findBy(['routeName' => 'frontend_artwork_details']);
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
