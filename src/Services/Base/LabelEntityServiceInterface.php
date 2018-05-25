<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:59
 */

namespace Jinya\Services\Base;

use Jinya\Entity\Label;

interface LabelEntityServiceInterface
{
    /**
     * Gets the specified amount of entities by keyword and label
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     *
     * @return array
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', Label $label = null): array;

    /**
     * Counts all entities based on keyword and label
     *
     * @param string $keyword
     * @param Label|null $label
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword = '', Label $label = null): int;
}
