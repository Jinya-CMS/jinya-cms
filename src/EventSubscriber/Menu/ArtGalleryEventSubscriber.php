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
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Framework\Events\Galleries\ArtGalleryEvent;
use Jinya\Framework\Events\Priority;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Underscore\Types\Strings;

class ArtGalleryEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RoutingEntry[] */
    private $affectedRoutes;

    /**
     * ArtGalleryEventSubscriber constructor.
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
            ArtGalleryEvent::PRE_SAVE => 'onPreArtGallerySave',
            ArtGalleryEvent::POST_SAVE => ['onPostArtGallerySave', Priority::CRITICAL],
        ];
    }

    public function onPostArtGallerySave(ArtGalleryEvent $event)
    {
        foreach ($this->affectedRoutes as $affectedRoute) {
            $parameter = $affectedRoute->getRouteParameter();
            $parameter['slug'] = $event->getSlug();
            $affectedRoute->setRouteParameter($parameter);
        }

        $this->entityManager->flush();
    }

    /**
     * @param ArtGalleryEvent $event
     * @throws NonUniqueResultException
     */
    public function onPreArtGallerySave(ArtGalleryEvent $event)
    {
        $this->affectedRoutes = [];
        if (!empty($event->getArtGallery()->getId())) {
            $oldSlug = $this->entityManager
                ->createQueryBuilder()
                ->select('gallery.slug')
                ->from(ArtGallery::class, 'gallery')
                ->where('gallery.id = :id')
                ->setParameter('id', $event->getArtGallery()->getId())
                ->getQuery()
                ->getSingleScalarResult();

            $galleryRoutes = $this->entityManager
                ->createQueryBuilder()
                ->select('route')
                ->from(RoutingEntry::class, 'route')
                ->where("route.routeName = 'frontend_art_gallery_details'")
                ->orWhere("route.routeName = 'frontend_gallery_details'")
                ->getQuery()
                ->getResult();

            $this->affectedRoutes = array_filter($galleryRoutes, function (RoutingEntry $routingEntry) use ($oldSlug) {
                $parameter = $routingEntry->getRouteParameter();

                return array_key_exists(
                    'slug',
                    $parameter
                ) && Strings::lower($parameter['slug']) === Strings::lower($oldSlug);
            });
        }
    }
}
