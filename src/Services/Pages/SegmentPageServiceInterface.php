<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:41
 */

namespace Jinya\Services\Pages;

use Jinya\Entity\SegmentPage\SegmentPage;

interface SegmentPageServiceInterface
{
    /**
     * Gets the specified @param string $slug
     * @return SegmentPage
     * @see SegmentPage
     */
    public function get(string $slug): SegmentPage;

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return SegmentPage[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given @param SegmentPage $page
     * @return SegmentPage
     * @see SegmentPage
     */
    public function saveOrUpdate(SegmentPage $page): SegmentPage;

    /**
     * Deletes the given @param SegmentPage $entity
     * @see SegmentPage
     */
    public function delete(SegmentPage $entity): void;
}
