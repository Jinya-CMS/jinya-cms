<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:59
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Label\Label;

interface LabelEntityServiceInterface
{
    /**
     * Gets the specified amount of entities by keyword and label
     *
     * @param QueryBuilder $queryBuilder
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     * @return array
     */
    public function getAll(
        QueryBuilder $queryBuilder,
        int $offset = 0,
        int $count = 10,
        string $keyword = '',
        Label $label = null
    ): array;

    /**
     * Counts all entities based on keyword and label
     *
     * @param QueryBuilder $queryBuilder
     * @param string $keyword
     * @param Label|null $label
     * @return int
     */
    public function countAll(QueryBuilder $queryBuilder, string $keyword = '', Label $label = null): int;
}
