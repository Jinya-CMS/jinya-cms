<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 22:25
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Label\Label;
use Jinya\Services\Labels\LabelServiceInterface;

class LabelEntityService implements LabelEntityServiceInterface
{
    /** @var LabelServiceInterface */
    private $labelService;

    /**
     * LabelEntityService constructor.
     * @param LabelServiceInterface $labelService
     */
    public function __construct(LabelServiceInterface $labelService)
    {
        $this->labelService = $labelService;
    }

    /**
     * Gets the specified amount of entities by keyword and label
     *
     * @param QueryBuilder $queryBuilder
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label $label
     * @return array
     */
    public function getAll(
        QueryBuilder $queryBuilder,
        int $offset = 0,
        int $count = 10,
        string $keyword = '',
        Label $label = null
    ): array {
        return $this->getFilteredQueryBuilder($queryBuilder, $keyword, $label)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a @param QueryBuilder $queryBuilder
     * @param string $keyword
     * @param Label|null $label
     * @return QueryBuilder
     * @see QueryBuilder with a keyword filter
     */
    protected function getFilteredQueryBuilder(
        QueryBuilder $queryBuilder,
        string $keyword,
        Label $label = null
    ): QueryBuilder {
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
     * @param QueryBuilder $queryBuilder
     * @param string $keyword
     * @param Label $label
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(QueryBuilder $queryBuilder, string $keyword = '', Label $label = null): int
    {
        return $this->getFilteredQueryBuilder($queryBuilder, $keyword, $label)
            ->select($queryBuilder->expr()->count('entity'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
