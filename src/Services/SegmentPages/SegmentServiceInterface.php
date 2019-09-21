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
     * Saves the artwork in the given segment page at the given position
     *
     * @param string $artworkSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveArtworkSegment(
        string $artworkSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the art gallery in the given segment page at the given position
     *
     * @param string $artGallerySlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveArtGallerySegment(
        string $artGallerySlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the video in the given segment page at the given position
     *
     * @param string $videoSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveVideoSegment(
        string $videoSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the youtube video in the given segment page at the given position
     *
     * @param string $youtubeVideoSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveYoutubeVideoSegment(
        string $youtubeVideoSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the video gallery in the given segment page at the given position
     *
     * @param string $videoGallerySlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveVideoGallerySegment(
        string $videoGallerySlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment;

    /**
     * Saves the form in the given segment page at the given position
     *
     * @param string $formSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
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
     *
     * @param string $html
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
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
     *
     * @param string $gallerySlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
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
     *
     * @param int $fileId
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
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
     * @param int $segmentId
     * @param string $action
     * @param string $target
     * @param string $script
     */
    public function updateAction(int $segmentId, ?string $action, ?string $target = '', ?string $script = ''): void;

    /**
     * Sets the segments position to the new position
     *
     * @param string $segmentPageSlug
     * @param int $segmentId
     * @param int $newPosition
     * @param int $oldPosition
     */
    public function updatePosition(string $segmentPageSlug, int $segmentId, int $oldPosition, int $newPosition): void;

    /**
     * Updates the artwork in the given segment page at the given position
     *
     * @param string $artworkSlug
     * @param int $segmentId
     * @return int
     */
    public function updateArtworkSegment(string $artworkSlug, int $segmentId): int;

    /**
     * Updates the art gallery in the given segment page at the given position
     *
     * @param string $artGallerySlug
     * @param int $segmentId
     * @return int
     */
    public function updateArtGallerySegment(string $artGallerySlug, int $segmentId): int;

    /**
     * Updates the video in the given segment page at the given position
     *
     * @param string $videoSlug
     * @param int $segmentId
     * @return int
     */
    public function updateVideoSegment(string $videoSlug, int $segmentId): int;

    /**
     * Updates the gallery in the given segment page at the given position
     *
     * @param string $gallerySlug
     * @param int $segmentId
     * @return int
     */
    public function updateGallerySegment(string $gallerySlug, int $segmentId): int;

    /**
     * Updates the file in the given segment page at the given position
     *
     * @param int $fileId
     * @param int $segmentId
     * @return int
     */
    public function updateFileSegment(int $fileId, int $segmentId): int;

    /**
     * Updates the youtube video in the given segment page at the given position
     *
     * @param string $youtubeVideoSlug
     * @param int $segmentId
     * @return int
     */
    public function updateYoutubeVideoSegment(string $youtubeVideoSlug, int $segmentId): int;

    /**
     * Updates the video gallery in the given segment page at the given position
     *
     * @param string $videoGallerySlug
     * @param int $segmentId
     * @return int
     */
    public function updateVideoGallerySegment(string $videoGallerySlug, int $segmentId): int;

    /**
     * Updates the form in the given segment page at the given position
     *
     * @param string $formSlug
     * @param int $segmentId
     * @return int
     */
    public function updateFormSegment(string $formSlug, int $segmentId): int;

    /**
     * Updates the html in the given segment page at the given position
     *
     * @param string $html
     * @param int $segmentId
     * @return int
     */
    public function updateHtmlSegment(string $html, int $segmentId): int;

    /**
     * Deletes the given segment
     *
     * @param int $id
     */
    public function deleteSegment(int $id): void;

    /**
     * Gets the segment with the specified id
     *
     * @param $id
     * @return Segment
     */
    public function get(int $id): Segment;
}
