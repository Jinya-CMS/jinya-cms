<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:41
 */

namespace Jinya\Services\Pages;

use Jinya\Entity\Page\Page;

interface PageServiceInterface
{
    /**
     * Gets the specified @param string $slug
     * @see Page by slug
     */
    public function get(string $slug): Page;

    /**
     * Gets all entities by the given parameters
     *
     * @return Page[]
     */
    public function getAll(string $keyword = ''): array;

    /**
     * Counts all entities
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given @param Page $page
     * @see Page
     */
    public function saveOrUpdate(Page $page): Page;

    /**
     * Deletes the given @param Page $entity
     * @see Page
     */
    public function delete(Page $entity): void;
}
