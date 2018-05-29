<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 22:25
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Label;
use Jinya\Services\Labels\LabelServiceInterface;

trait LabelEntityServiceTrait
{
    /** @var LabelServiceInterface */
    protected $labelService;

    /**
     * Gets the specified amount of entities by keyword and label
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     * @return array
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', Label $label = null): array
    {
        return $this->getFilteredQueryBuilder($keyword, $label)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a @see QueryBuilder with a keyword filter
     *
     * @param string $keyword
     * @param Label|null $label
     * @return QueryBuilder
     */
    protected function getFilteredQueryBuilder(string $keyword, Label $label = null): QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder = $queryBuilder
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('entity.description', ':keyword'),
                $queryBuilder->expr()->like('entity.name', ':keyword')
            ))
            ->setParameter('keyword', "%$keyword%");

        if (null !== $label) {
            $queryBuilder->andWhere(':label_id MEMBER OF entity.labels')->setParameter('label_id', $label->getId());
        }

        return $queryBuilder;
    }

    /**
     * Counts all entities based on keyword and label
     *
     * @param string $keyword
     * @param Label|null $label
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword = '', Label $label = null): int
    {
        $queryBuilder = $this->getFilteredQueryBuilder($keyword, $label);

        return $queryBuilder
            ->select($queryBuilder->expr()->count('entity'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
