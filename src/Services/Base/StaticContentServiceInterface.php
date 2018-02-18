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
     * @inheritdoc
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * @inheritdoc
     */
    public function countAll(string $keyword = ''): int;
}