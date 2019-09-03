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
     * Gets a list of videos in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return YoutubeVideo[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Counts all videos filtered by the given keyword
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or update the given video
     *
     * @param YoutubeVideo $video
     * @return YoutubeVideo
     */
    public function saveOrUpdate(YoutubeVideo $video): YoutubeVideo;

    /**
     * Deletes the given video
     *
     * @param YoutubeVideo $video
     */
    public function delete(YoutubeVideo $video): void;

    /**
     * Gets the video by slug
     *
     * @param string $slug
     * @return YoutubeVideo
     */
    public function get(string $slug): YoutubeVideo;
}
