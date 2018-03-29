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
use Jinya\Entity\ArtworkPosition;
use Jinya\Entity\Gallery;
use Jinya\Services\Galleries\GalleryServiceInterface;
use function array_splice;
use function array_walk;

class ArtworkPositionService implements ArtworkPositionServiceInterface
{
    /** @var GalleryServiceInterface */
    private $galleryService;
    /** @var ArtworkServiceInterface */
    private $artworkService;
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ArtworkPositionService constructor.
     * @param GalleryServiceInterface $galleryService
     * @param ArtworkServiceInterface $artworkService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(GalleryServiceInterface $galleryService, ArtworkServiceInterface $artworkService, EntityManagerInterface $entityManager)
    {
        $this->galleryService = $galleryService;
        $this->artworkService = $artworkService;
        $this->entityManager = $entityManager;
    }

    /**
     * Saves the artwork in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @param int $position
     * @return bool
     */
    public function savePosition(string $gallerySlug, string $artworkSlug, int $position): bool
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $artwork = $this->artworkService->get($artworkSlug);

        $artworkPosition = new ArtworkPosition();
        $artworkPosition->setArtwork($artwork);
        $artworkPosition->setGallery($gallery);

        $this->rearrangeArtworks(-1, $position, $artworkPosition, $gallery);

        return true;
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @param ArtworkPosition $artworkPosition
     * @param Gallery $gallery
     * @return void
     */
    private function rearrangeArtworks(int $oldPosition, int $newPosition, ArtworkPosition $artworkPosition, Gallery $gallery): void
    {
        $positions = $gallery->getArtworks()->toArray();
        uasort($positions, function ($a, $b) {
            /** @var ArtworkPosition $a */
            /** @var ArtworkPosition $b */
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        if ($oldPosition < $newPosition) {
            array_splice($positions, $newPosition + 1, 0, [$artworkPosition]);

            if ($oldPosition > -1) {
                array_splice($positions, $oldPosition, 1);
            }
        } else {
            array_splice($positions, $newPosition, 0, [$artworkPosition]);

            if ($oldPosition > -1) {
                array_splice($positions, $oldPosition + 1, 1);
            }
        }

        array_walk($positions, function (ArtworkPosition &$item, int $index) {
            $item->setPosition($index);
        });

        $gallery->setArtworks(new ArrayCollection($positions));
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function updatePosition(string $gallerySlug, int $artworkPositionId, int $oldPosition, int $newPosition)
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $artworks = $gallery->getArtworks();

        $artwork = $artworks->filter(function (ArtworkPosition $item) use ($oldPosition) {
            return $item->getPosition() === $oldPosition;
        })->first();

        $this->rearrangeArtworks($oldPosition, $newPosition, $artwork, $gallery);
    }

    /**
     * Deletes the given artwork position
     *
     * @param int $id
     * @return void
     */
    public function deletePosition(int $id)
    {
        $this->entityManager
            ->createQueryBuilder()
            ->delete(ArtworkPosition::class, 'e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    /**
     * Gets the artwork position for the given id
     *
     * @param int $id
     * @return ArtworkPosition
     */
    public function getPosition(int $id): ArtworkPosition
    {
        return $this->entityManager->find(ArtworkPosition::class, $id);
    }

    /**
     * Sets the artwork of the given artwork position to the new slug
     *
     * @param int $id
     * @param string $artworkSlug
     * @return void
     */
    public function updateArtwork(int $id, string $artworkSlug)
    {
        $artwork = $this->artworkService->get($artworkSlug);
        $this->entityManager
            ->createQueryBuilder()
            ->update(ArtworkPosition::class, 'e')
            ->set('e.artwork', $artwork->getId())
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}