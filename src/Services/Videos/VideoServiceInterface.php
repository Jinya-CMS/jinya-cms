<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 06.06.18
 * Time: 18:51
 */

namespace Jinya\Services\Videos;

use Jinya\Entity\Video\Video;

interface VideoServiceInterface
{
    /**
     * Gets a list of videos in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Video[]
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
     * @param Video $video
     * @return Video
     */
    public function saveOrUpdate(Video $video): Video;

    /**
     * Deletes the given video
     *
     * @param Video $video
     */
    public function delete(Video $video): void;

    /**
     * Gets the video by slug or id
     *
     * @param string $slug
     * @return Video
     */
    public function get(string $slug): ?Video;
}
