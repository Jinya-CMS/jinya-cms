<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:27
 */

namespace Jinya\Services\SegmentPages;

use Jinya\Entity\SegmentPage\SegmentPage;

interface SegmentPageServiceInterface
{
    /**
     * Gets a list of segmentPages in the given range and filtered by the given keyword
     *
     * @return SegmentPage[]
     */
    public function getAll(string $keyword = ''): array;

    /**
     * Counts all segmentPages filtered by the given keyword
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or update the given segmentPage
     */
    public function saveOrUpdate(SegmentPage $artwork): SegmentPage;

    /**
     * Deletes the given gallery
     */
    public function delete(SegmentPage $artwork): void;

    /**
     * Gets the segmentPage by slug or id
     */
    public function get(string $slug): SegmentPage;
}
