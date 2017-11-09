<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace DataBundle\Services\Galleries;


use DataBundle\Entity\Gallery;

interface GalleryServiceInterface
{
    /**
     * Gets the specified gallery, either by slug or id
     *
     * @param string|int $idOrSlug
     * @return Gallery
     */
    public function get($idOrSlug): ?Gallery;

    /**
     * Gets the specified gallery by id
     *
     * @param int $id
     * @return Gallery
     */
    public function getById(int $id): ?Gallery;

    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return Gallery,
     */
    public function getBySlug(string $slug): ?Gallery;

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Gallery[]
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = ''): array;

    /**
     * Counts all galleries
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given gallery
     *
     * @param Gallery $gallery
     * @return Gallery
     */
    public function saveOrUpdate(Gallery $gallery): Gallery;

    /**
     * Deletes the given gallery
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id);
}