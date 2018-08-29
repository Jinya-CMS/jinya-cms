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
use Jinya\Framework\Events\Artworks\ArtworkEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArtworkCacheSubscriber implements EventSubscriberInterface
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
            ArtworkEvent::POST_SAVE => 'onArtworkSave',
        ];
    }

    public function onArtworkSave(ArtworkEvent $event)
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getSlug(), CacheBuilderInterface::ARTWORK);
        $galleries = $this->entityManager->createQueryBuilder()
            ->select('gallery.slug')
            ->from(ArtGallery::class, 'gallery')
            ->join('gallery.artworks', 'position')
            ->join('position.artwork', 'artwork')
            ->where('artwork.slug = :slug')
            ->setParameter('slug', $event->getSlug())
            ->getQuery()
            ->getScalarResult();

        foreach ($galleries as $gallery) {
            $this->cacheBuilder->buildCacheBySlugAndType($gallery['slug'], CacheBuilderInterface::ART_GALLERY);
            $this->cacheBuilder->buildCacheBySlugAndType($gallery['slug'], CacheBuilderInterface::GALLERY);
        }
    }
}