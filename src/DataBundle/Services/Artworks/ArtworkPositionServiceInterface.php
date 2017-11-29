<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:08
 */

namespace DataBundle\Services\Artworks;


interface ArtworkPositionServiceInterface
{
    /**
     * Saves the artwork in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @param int $position
     * @return bool
     */
    public function savePosition(string $gallerySlug, string $artworkSlug, int $position): bool;

    /**
     * Sets the artworks position to the new position
     *
     * @param string $gallerySlug
     * @param int $artworkPositionId
     * @param int $newPosition
     * @return mixed
     */
    public function updatePosition(string $gallerySlug, int $artworkPositionId, int $newPosition);
}