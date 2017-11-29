<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:27
 */

namespace DataBundle\Services\Artworks;

use DataBundle\Entity\Artwork;
use DataBundle\Entity\ArtworkPosition;
use DataBundle\Services\Base\BaseArtServiceInterface;

interface ArtworkServiceInterface extends BaseArtServiceInterface
{
    /**
     * Gets an artwork by the given id
     *
     * @param int $id
     * @return Artwork
     */
    public function getById(int $id): ?Artwork;

    /**
     * Gets a list of artworks in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Artwork[]
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = ''): array;

    /**
     * Counts all artworks filtered by the given keyword
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or update the given artwork
     *
     * @param Artwork $artwork
     * @return Artwork
     */
    public function saveOrUpdate(Artwork $artwork): ?Artwork;

    /**
     * Deletes the given artwork
     *
     * @param int $id
     */
    public function delete(int $id): void;

    /**
     * Gets the artwork for the given slug
     *
     * @param string $slug
     * @return Artwork
     */
    public function getBySlug(string $slug): ?Artwork;

    /**
     * Gets the artwork by slug or id
     *
     * @param $idOrSlug string|int
     * @return Artwork
     */
    public function get($idOrSlug): ?Artwork;
}