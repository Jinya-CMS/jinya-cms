<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:08
 */

namespace Jinya\Services\Artworks;

use Jinya\Entity\Artwork\ArtworkPosition;

interface ArtworkPositionServiceInterface
{
    /**
     * Saves the artwork in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @param int $position
     * @return int
     */
    public function savePosition(string $gallerySlug, string $artworkSlug, int $position): int;

    /**
     * Sets the artworks position to the new position
     *
     * @param string $gallerySlug
     * @param int $artworkPositionId
     * @param int $newPosition
     * @param int $oldPosition
     */
    public function updatePosition(
        string $gallerySlug,
        int $artworkPositionId,
        int $oldPosition,
        int $newPosition
    ): void;

    /**
     * Deletes the given artwork position
     *
     * @param int $id
     */
    public function deletePosition(int $id);

    /**
     * Gets the artwork position for the given id
     *
     * @param int $id
     * @return ArtworkPosition
     */
    public function getPosition(int $id): ArtworkPosition;

    /**
     * Sets the artwork of the given artwork position to the new slug
     *
     * @param int $id
     * @param string $artworkSlug
     */
    public function updateArtwork(int $id, string $artworkSlug): void;
}
