<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 01.06.18
 * Time: 19:02
 */

namespace Jinya\Services\Videos;

use Jinya\Entity\Video\YoutubeVideo;

interface YoutubeVideoServiceInterface
{

    /**
     * Gets a list of artworks in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return YoutubeVideo[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

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
     * @param YoutubeVideo $video
     * @return YoutubeVideo
     */
    public function saveOrUpdate(YoutubeVideo $video): YoutubeVideo;

    /**
     * Deletes the given gallery
     *
     * @param YoutubeVideo $video
     */
    public function delete(YoutubeVideo $video): void;

    /**
     * Gets the artwork by slug or id
     *
     * @param string $slug
     * @return YoutubeVideo
     */
    public function get(string $slug): ?YoutubeVideo;
}