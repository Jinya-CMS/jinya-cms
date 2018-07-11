<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace Jinya\Services\Galleries;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Label\Label;
use Jinya\Services\Base\LabelEntityServiceTrait;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;

class ArtGalleryService implements ArtGalleryServiceInterface
{

    /**
     * GalleryService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param LabelServiceInterface $labelService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, LabelServiceInterface $labelService)
    {
        $this->labelService = $labelService;
    }

    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return ArtGallery
     */
    public function get(string $slug)
    {
        // TODO: Implement get() method.
    }

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     * @return ArtGallery[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', Label $label = null): array
    {
        // TODO: Implement getAll() method.
    }

    /**
     * Counts all galleries
     *
     * @param string $keyword
     * @param \Jinya\Entity\Label\Label|null $label
     * @return int
     */
    public function countAll(string $keyword = '', Label $label = null): int
    {
        // TODO: Implement countAll() method.
    }

    /**
     * Saves or updates the given gallery
     *
     * @param ArtGallery $gallery
     * @return ArtGallery
     */
    public function saveOrUpdate(ArtGallery $gallery)
    {
        // TODO: Implement saveOrUpdate() method.
    }

    /**
     * Deletes the given gallery
     *
     * @param ArtGallery $gallery
     */
    public function delete(ArtGallery $gallery): void
    {
        // TODO: Implement delete() method.
    }

    /**
     * Updates the given field
     *
     * @param string $key
     * @param string $value
     * @param int $id
     */
    public function updateField(string $key, string $value, int $id)
    {
        // TODO: Implement updateField() method.
    }
}
