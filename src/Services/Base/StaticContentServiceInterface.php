<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 17:01
 */

namespace Jinya\Services\Base;

interface StaticContentServiceInterface
{
    /**
     * Gets a list of static content elements
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Gets a single static content element
     *
     * @return mixed
     */
    public function get(string $slug);

    /**
     * Counts all static content elements with the given keyword
     */
    public function countAll(string $keyword = ''): int;
}
