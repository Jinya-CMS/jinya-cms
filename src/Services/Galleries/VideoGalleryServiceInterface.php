<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace Jinya\Services\Galleries;

use Jinya\Entity\Gallery\VideoGallery;

interface VideoGalleryServiceInterface
{
    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return VideoGallery
     */
    public function get(string $slug): VideoGallery;

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return VideoGallery[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

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
     * @param VideoGallery $gallery
     * @return VideoGallery
     */
    public function saveOrUpdate(VideoGallery $gallery): \Jinya\Entity\Gallery\VideoGallery;

    /**
     * Deletes the given gallery
     *
     * @param VideoGallery $gallery
     */
    public function delete(VideoGallery $gallery): void;
}
