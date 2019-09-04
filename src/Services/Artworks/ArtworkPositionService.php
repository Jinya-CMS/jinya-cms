<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:09
 */

namespace Jinya\Services\Artworks;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Framework\Events\Artworks\ArtworkPositionDeleteEvent;
use Jinya\Framework\Events\Artworks\ArtworkPositionEvent;
use Jinya\Framework\Events\Artworks\ArtworkPositionUpdateArtworkEvent;
use Jinya\Framework\Events\Artworks\ArtworkPositionUpdateEvent;
use Jinya\Framework\Events\Artworks\RearrangeEvent;
use Jinya\Services\Base\ArrangementServiceTrait;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ArtworkPositionService implements ArtworkPositionServiceInterface
{
    use ArrangementServiceTrait;

    /** @var ArtGalleryServiceInterface */
    private $galleryService;

    /** @var ArtworkServiceInterface */
    private $artworkService;

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * ArtworkPositionService constructor.
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtworkServiceInterface $artworkService
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ArtGalleryServiceInterface $galleryService,
        ArtworkServiceInterface $artworkService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->galleryService = $galleryService;
        $this->artworkService = $artworkService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Saves the artwork in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @param int $position
     * @return int
     */
    public function savePosition(string $gallerySlug, string $artworkSlug, int $position): int
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $artwork = $this->artworkService->get($artworkSlug);

        $artworkPosition = new ArtworkPosition();
        $artworkPosition->setArtwork($artwork);
        $artworkPosition->setGallery($gallery);

        $pre = $this->eventDispatcher->dispatch(
            new ArtworkPositionEvent($artworkPosition, -1),
            ArtworkPositionEvent::PRE_SAVE
        );

        if (!$pre->isCancel()) {
            $this->rearrangeArtworks(-1, $position, $artworkPosition, $gallery);

            $this->eventDispatcher->dispatch(
                new ArtworkPositionEvent($artworkPosition, $artworkPosition->getId()),
                ArtworkPositionEvent::POST_SAVE
            );
        }

        return $artworkPosition->getId();
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @param ArtworkPosition $artworkPosition
     * @param ArtGallery $gallery
     */
    private function rearrangeArtworks(
        int $oldPosition,
        int $newPosition,
        ArtworkPosition $artworkPosition,
        ArtGallery $gallery
    ): void {
        $pre = $this->eventDispatcher->dispatch(
            new RearrangeEvent($gallery, $artworkPosition, $oldPosition, $newPosition),
            RearrangeEvent::PRE_REARRANGE
        );

        if (!$pre->isCancel()) {
            $positions = $gallery->getArtworks()->toArray();
            $positions = $this->rearrange($positions, $oldPosition, $newPosition, $artworkPosition);

            $gallery->setArtworks(new ArrayCollection($positions));
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                new RearrangeEvent($gallery, $artworkPosition, $oldPosition, $newPosition),
                RearrangeEvent::POST_REARRANGE
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updatePosition(
        string $gallerySlug,
        int $artworkPositionId,
        int $oldPosition,
        int $newPosition
    ): void {
        $pre = $this->eventDispatcher->dispatch(
            new ArtworkPositionUpdateEvent($gallerySlug, $artworkPositionId, $oldPosition, $newPosition),
            ArtworkPositionUpdateEvent::PRE_UPDATE
        );

        if (!$pre->isCancel()) {
            $gallery = $this->galleryService->get($gallerySlug);
            $artworks = $gallery->getArtworks();

            $artwork = $artworks->filter(static function (ArtworkPosition $item) use ($oldPosition) {
                return $item->getPosition() === $oldPosition;
            })->first();

            $this->rearrangeArtworks($oldPosition, $newPosition, $artwork, $gallery);
            $this->eventDispatcher->dispatch(
                new ArtworkPositionUpdateEvent($gallerySlug, $artworkPositionId, $oldPosition, $newPosition),
                ArtworkPositionUpdateEvent::POST_UPDATE
            );
        }
    }

    /**
     * Deletes the given artwork position
     *
     * @param int $id
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function deletePosition(int $id): void
    {
        $gallery = $this->entityManager
            ->createQueryBuilder()
            ->select('gallery')
            ->from(ArtGallery::class, 'gallery')
            ->join('gallery.artworks', 'position')
            ->where('position.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
        $pre = $this->eventDispatcher->dispatch(
            new ArtworkPositionDeleteEvent($gallery, $id),
            ArtworkPositionDeleteEvent::PRE_DELETE
        );

        if (!$pre->isCancel()) {
            $this->entityManager
                ->createQueryBuilder()
                ->delete(ArtworkPosition::class, 'e')
                ->where('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->execute();
            $this->eventDispatcher->dispatch(
                new ArtworkPositionDeleteEvent($gallery, $id),
                ArtworkPositionDeleteEvent::POST_DELETE
            );
        }
    }

    /**
     * Gets the artwork position for the given id
     *
     * @param int $id
     * @return ArtworkPosition
     */
    public function getPosition(int $id): ArtworkPosition
    {
        $this->eventDispatcher->dispatch(new ArtworkPositionEvent(null, $id), ArtworkPositionEvent::PRE_GET);
        $position = $this->entityManager->find(ArtworkPosition::class, $id);
        $this->eventDispatcher->dispatch(new ArtworkPositionEvent($position, $id), ArtworkPositionEvent::POST_GET);

        return $position;
    }

    /**
     * Sets the artwork of the given artwork position to the new slug
     *
     * @param int $id
     * @param string $artworkSlug
     */
    public function updateArtwork(int $id, string $artworkSlug): void
    {
        $artwork = $this->artworkService->get($artworkSlug);
        $pre = $this->eventDispatcher->dispatch(
            new ArtworkPositionUpdateArtworkEvent($artwork, $id),
            ArtworkPositionUpdateArtworkEvent::PRE_UPDATE_ARTWORK
        );

        if (!$pre->isCancel()) {
            $this->entityManager
                ->createQueryBuilder()
                ->update(ArtworkPosition::class, 'e')
                ->set('e.artwork', $artwork->getId())
                ->where('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->execute();

            $this->eventDispatcher->dispatch(
                new ArtworkPositionUpdateArtworkEvent($artwork, $id),
                ArtworkPositionUpdateArtworkEvent::POST_UPDATE_ARTWORK
            );
        }
    }
}
