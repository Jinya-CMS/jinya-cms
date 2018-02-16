<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:09
 */

namespace Jinya\Services\Artworks;


use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\ArtworkPosition;
use Jinya\Entity\Gallery;
use Jinya\Services\Galleries\GalleryServiceInterface;

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

        $position = $this->rearrangeArtworks($position, $gallery);

        $artworkPosition = new ArtworkPosition();
        $artworkPosition->setArtwork($artwork);
        $artworkPosition->setPosition($position);
        $artworkPosition->setGallery($gallery);

        $this->entityManager->persist($artworkPosition);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $position
     * @param $gallery
     * @return int
     */
    private function rearrangeArtworks(int $position, Gallery $gallery): int
    {
        $positions = $gallery->getArtworks()->toArray();
        uasort($positions, function ($a, $b) {
            /** @var ArtworkPosition $a */
            /** @var ArtworkPosition $b */
            return ($a->getPosition() > $b->getPosition()) ? -1 : 1;
        });
        array_map(function ($item) {
            /** @var ArtworkPosition $item */
            return $item->getPosition();
        }, $positions);

        if ($position === -1) {
            $position = array_shift($positions)->getPosition() + 1;
        }

        /** @var ArtworkPosition $artworkPosition */
        foreach ($gallery->getArtworks() as $artworkPosition) {
            if ($artworkPosition->getPosition() >= $position) {
                $artworkPosition->setPosition($artworkPosition->getPosition() + 1);
            }
        }

        $artworks = $gallery->getArtworks();

        /** @var ArrayIterator $iterator */
        $iterator = $artworks->getIterator();
        $iterator->uasort(function (ArtworkPosition $a, ArtworkPosition $b) {
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });
        $gallery->setArtworks(new ArrayCollection(iterator_to_array($iterator)));

        return $position;
    }

    /**
     * @inheritdoc
     */
    public function updatePosition(string $gallerySlug, int $artworkPositionId, int $newPosition)
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $artworks = $gallery->getArtworks();

        $this->rearrangeArtworks($newPosition, $gallery);

        foreach ($artworks as $artworkPosition) {
            if ($artworkPosition->getId() === $artworkPositionId) {
                $artworkPosition->setPosition($newPosition);
            }
        }

        $this->entityManager->flush();
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
            ->getRepository(ArtworkPosition::class)
            ->createQueryBuilder('e')
            ->delete()
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
            ->getRepository(ArtworkPosition::class)
            ->createQueryBuilder('e')
            ->update()
            ->set('e.artwork', $artwork->getId())
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}