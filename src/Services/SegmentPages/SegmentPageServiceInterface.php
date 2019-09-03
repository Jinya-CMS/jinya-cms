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
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return SegmentPage[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Counts all segmentPages filtered by the given keyword
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or update the given segmentPage
     *
     * @param SegmentPage $artwork
     * @return SegmentPage
     */
    public function saveOrUpdate(SegmentPage $artwork): SegmentPage;

    /**
     * Deletes the given gallery
     *
     * @param SegmentPage $artwork
     */
    public function delete(SegmentPage $artwork): void;

    /**
     * Gets the segmentPage by slug or id
     *
     * @param string $slug
     * @return SegmentPage
     */
    public function get(string $slug): SegmentPage;
}
