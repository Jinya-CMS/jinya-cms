<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:08
 */

namespace Jinya\Services\Media;

use Jinya\Entity\Media\GalleryFilePosition;

interface GalleryFilePositionServiceInterface
{
    /**
     * Saves the file in the given gallery at the given position
     *
     * @param int $fileId
     * @param string $gallerySlug
     * @param int $position
     * @return int
     */
    public function savePosition(int $fileId, string $gallerySlug, int $position): int;

    /**
     * Sets the files position to the new position
     *
     * @param string $gallerySlug
     * @param int $galleryFilePositionId
     * @param int $newPosition
     * @param int $oldPosition
     */
    public function updatePosition(
        string $gallerySlug,
        int $galleryFilePositionId,
        int $oldPosition,
        int $newPosition
    ): void;

    /**
     * Deletes the given gallery file position
     *
     * @param int $id
     */
    public function deletePosition(int $id): void;

    /**
     * Gets the gallery file position for the given id
     *
     * @param int $id
     * @return GalleryFilePosition
     */
    public function getPosition(int $id): GalleryFilePosition;

    /**
     * Sets the file of the given gallery file position to the new id
     *
     * @param int $id
     * @param int $fileId
     */
    public function updateArtwork(int $id, int $fileId): void;
}
