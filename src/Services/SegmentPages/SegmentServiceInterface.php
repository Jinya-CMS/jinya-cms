<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:27
 */

namespace Jinya\Services\SegmentPages;

use Jinya\Entity\SegmentPage\Segment;

interface SegmentServiceInterface
{
    /**
     * Saves the form in the given segment page at the given position
     */
    public function saveFormSegment(
        string $formSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the html in the given segment page at the given position
     */
    public function saveHtmlSegment(
        string $html,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the gallery in the given segment page at the given position
     */
    public function saveGallerySegment(
        string $gallerySlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the file in the given segment page at the given position
     */
    public function saveFileSegment(
        int $fileId,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * @param string $action
     * @param string $target
     * @param string $script
     */
    public function updateAction(int $segmentId, ?string $action, ?string $target = '', ?string $script = ''): void;

    /**
     * Sets the segments position to the new position
     */
    public function updatePosition(string $segmentPageSlug, int $segmentId, int $oldPosition, int $newPosition): void;

    /**
     * Updates the gallery in the given segment page at the given position
     */
    public function updateGallerySegment(string $gallerySlug, int $segmentId): int;

    /**
     * Updates the file in the given segment page at the given position
     */
    public function updateFileSegment(int $fileId, int $segmentId, string $script, string $action, string $target): int;

    /**
     * Updates the form in the given segment page at the given position
     */
    public function updateFormSegment(string $formSlug, int $segmentId): int;

    /**
     * Updates the html in the given segment page at the given position
     */
    public function updateHtmlSegment(string $html, int $segmentId): int;

    /**
     * Deletes the given segment
     */
    public function deleteSegment(int $id): void;

    /**
     * Gets the segment with the specified id
     *
     * @param $id
     */
    public function get(int $id): Segment;
}
