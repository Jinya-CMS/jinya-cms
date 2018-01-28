<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 18:03
 */

namespace ServiceBundle\Services\Base;

use DataBundle\Entity\SlugEntity;

interface BaseStaticContentServiceInterface
{
    /**
     * Gets the specified SlugEntity, either by slug or id
     *
     * @param string|int $idOrSlug
     * @return SlugEntity
     */
    public function get($idOrSlug);

    /**
     * Gets the specified SlugEntity by id
     *
     * @param int $id
     * @return SlugEntity
     */
    public function getById(int $id);

    /**
     * Gets the specified SlugEntity by slug
     *
     * @param string $slug
     * @return SlugEntity
     */
    public function getBySlug(string $slug);

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return SlugEntity[]
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = ''): array;

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given SlugEntity
     *
     * @param SlugEntity $slugEntity
     * @return SlugEntity
     */
    function save($slugEntity);

    /**
     * Deletes the given SlugEntity
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id);

    /**
     * Updates the given field
     *
     * @param string $key
     * @param string $value
     * @param int $id
     * @return void
     */
    public function updateField(string $key, string $value, int $id);
}