<?php

namespace Jinya\Services\Media;

use Jinya\Entity\Media\Gallery;

interface GalleryServiceInterface
{
    /**
     * Gets a list of all galleries in the gallery and filtered by the given keyword and tag
     *
     * @return Gallery[]
     */
    public function getAll(string $keyword = '', string $tag = ''): array;

    /**
     * Counts all galleries filtered by the given keyword in the given gallery and tag
     */
    public function countAll(string $keyword = '', string $tag = ''): int;

    /**
     * Saves or update the given gallery
     */
    public function saveOrUpdate(Gallery $gallery): Gallery;

    /**
     * Deletes the given gallery
     */
    public function delete(Gallery $gallery): void;

    /**
     * Gets the gallery by id
     *
     * @param $idOrSlug
     */
    public function get($idOrSlug): ?Gallery;

    /**
     * Gets the gallery by slug
     */
    public function getBySlug(string $slug): ?Gallery;
}
