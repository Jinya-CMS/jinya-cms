<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 27.06.18
 * Time: 09:14
 */

namespace Jinya\Services\Videos;


interface AllVideoServiceInterface
{
    /**
     * Lists all videos of all types
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return array
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Counts all videos of all types
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;
}