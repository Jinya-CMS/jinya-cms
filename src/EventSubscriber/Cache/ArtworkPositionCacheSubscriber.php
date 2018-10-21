<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Framework\Events\Artworks\ArtworkPositionDeleteEvent;
use Jinya\Framework\Events\Artworks\ArtworkPositionEvent;
use Jinya\Framework\Events\Artworks\ArtworkPositionUpdateArtworkEvent;
use Jinya\Framework\Events\Artworks\ArtworkPositionUpdateEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArtworkPositionCacheSubscriber implements EventSubscriberInterface
{
    /** @var CacheBuilderInterface */
    private $cacheBuilder;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ArtworkCacheSubscriber constructor.
     * @param CacheBuilderInterface $cacheBuilder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(CacheBuilderInterface $cacheBuilder, EntityManagerInterface $entityManager)
    {
        $this->cacheBuilder = $cacheBuilder;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            ArtworkPositionEvent::POST_SAVE => 'onArtworkPositionSave',
            ArtworkPositionDeleteEvent::POST_DELETE => 'onArtworkPositionDelete',
            ArtworkPositionUpdateEvent::POST_UPDATE => 'onArtworkPositionUpdate',
            ArtworkPositionUpdateArtworkEvent::POST_UPDATE_ARTWORK => 'onArtworkPositionUpdateArtwork',
        ];
    }

    public function onArtworkPositionDelete(ArtworkPositionDeleteEvent $event)
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getGallery()->getSlug(), CacheBuilderInterface::ART_GALLERY);
        $this->cacheBuilder->buildCacheBySlugAndType($event->getGallery()->getSlug(), CacheBuilderInterface::GALLERY);
    }

    public function onArtworkPositionSave(ArtworkPositionEvent $event)
    {
        $artworkPosition = $event->getArtworkPosition();
        $this->cacheBuilder->buildCacheBySlugAndType($artworkPosition->getArtwork()->getSlug(), CacheBuilderInterface::ARTWORK);
        $this->cacheBuilder->buildCacheBySlugAndType($artworkPosition->getGallery()->getSlug(), CacheBuilderInterface::ART_GALLERY);
        $this->cacheBuilder->buildCacheBySlugAndType($artworkPosition->getGallery()->getSlug(), CacheBuilderInterface::GALLERY);
    }

    public function onArtworkPositionUpdate(ArtworkPositionUpdateEvent $event)
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getGallerySlug(), CacheBuilderInterface::ART_GALLERY);
        $this->cacheBuilder->buildCacheBySlugAndType($event->getGallerySlug(), CacheBuilderInterface::GALLERY);
    }

    public function onArtworkPositionUpdateArtwork(ArtworkPositionUpdateArtworkEvent $event)
    {
        $galleries = $this->entityManager->createQueryBuilder()
            ->select('gallery.slug')
            ->from(ArtGallery::class, 'gallery')
            ->join('gallery.artworks', 'position')
            ->where('position.id = :id')
            ->setParameter('id', $event->getPositionId())
            ->getQuery()
            ->getScalarResult();

        foreach ($galleries as $gallery) {
            $this->cacheBuilder->buildCacheBySlugAndType($gallery['slug'], CacheBuilderInterface::ART_GALLERY);
            $this->cacheBuilder->buildCacheBySlugAndType($gallery['slug'], CacheBuilderInterface::GALLERY);
        }
    }
}
