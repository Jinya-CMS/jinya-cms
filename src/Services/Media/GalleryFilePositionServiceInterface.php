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
     */
    public function savePosition(int $fileId, int $galleryId, int $position): int;

    /**
     * Sets the files position to the new position
     */
    public function updatePosition(
        string $gallerySlug,
        int $galleryFilePositionId,
        int $oldPosition,
        int $newPosition
    ): void;

    /**
     * Deletes the given gallery file position
     */
    public function deletePosition(int $id): void;

    /**
     * Gets the gallery file position for the given id
     */
    public function getPosition(int $id): GalleryFilePosition;

    /**
     * Sets the file of the given gallery file position to the new id
     */
    public function updateFile(int $id, int $fileId): void;
}
