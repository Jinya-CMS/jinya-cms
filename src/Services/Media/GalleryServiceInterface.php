<?php

namespace Jinya\Services\Media;

use Jinya\Entity\Media\Gallery;

interface GalleryServiceInterface
{
    /**
     * Gets a list of all galleries in the gallery and filtered by the given keyword and tag
     *
     * @param string $keyword
     * @param string $tag
     * @return Gallery[]
     */
    public function getAll(string $keyword = '', string $tag = ''): array;

    /**
     * Counts all galleries filtered by the given keyword in the given gallery and tag
     *
     * @param string $keyword
     * @param string $tag
     * @return int
     */
    public function countAll(string $keyword = '', string $tag = ''): int;

    /**
     * Saves or update the given gallery
     *
     * @param Gallery $gallery
     * @return Gallery
     */
    public function saveOrUpdate(Gallery $gallery): Gallery;

    /**
     * Deletes the given gallery
     *
     * @param Gallery $gallery
     */
    public function delete(Gallery $gallery): void;

    /**
     * Gets the gallery by slug or id
     *
     * @param int $id
     * @return Gallery
     */
    public function get(int $id): Gallery;
}
