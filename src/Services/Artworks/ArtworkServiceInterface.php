<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:27
 */

namespace Jinya\Services\Artworks;

use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Label\Label;

interface ArtworkServiceInterface
{
    /**
     * Gets a list of artworks in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     * @return Artwork[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', Label $label = null): array;

    /**
     * Counts all artworks filtered by the given keyword
     *
     * @param string $keyword
     * @param Label|null $label
     * @return int
     */
    public function countAll(string $keyword = '', Label $label = null): int;

    /**
     * Saves or update the given artwork
     *
     * @param Artwork $artwork
     * @return Artwork
     */
    public function saveOrUpdate(Artwork $artwork): Artwork;

    /**
     * Deletes the given gallery
     *
     * @param Artwork $artwork
     */
    public function delete(Artwork $artwork): void;

    /**
     * Gets the artwork by slug or id
     *
     * @param string $slug
     * @return Artwork
     */
    public function get(string $slug): ?Artwork;
}
