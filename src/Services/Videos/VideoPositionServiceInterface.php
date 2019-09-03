<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:08
 */

namespace Jinya\Services\Videos;

use Jinya\Entity\Video\VideoPosition;

interface VideoPositionServiceInterface
{
    /**
     * Saves the video in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $videoSlug
     * @param int $position
     * @param string $type
     * @return int
     */
    public function savePosition(string $gallerySlug, string $videoSlug, int $position, string $type): int;

    /**
     * Sets the videos position to the new position
     *
     * @param string $gallerySlug
     * @param int $videoPositionId
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function updatePosition(string $gallerySlug, int $videoPositionId, int $oldPosition, int $newPosition): void;

    /**
     * Deletes the given video position
     *
     * @param int $id
     */
    public function deletePosition(int $id): void;

    /**
     * Gets the video position for the given id
     *
     * @param int $id
     * @return VideoPosition
     */
    public function getPosition(int $id): VideoPosition;

    /**
     * Sets the video of the given video position to the new slug
     *
     * @param int $id
     * @param string $videoSlug
     * @param string $type
     */
    public function updateVideo(int $id, string $videoSlug, string $type): void;
}
