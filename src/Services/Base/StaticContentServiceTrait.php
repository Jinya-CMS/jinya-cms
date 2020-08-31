<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Trait StaticContentServiceTrait
 * @method getQueryBuilder()
 */
trait StaticContentServiceTrait
{
    /**
     * {@inheritdoc}
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        return $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a querybuilder with a keyword filter
     */
    protected function getFilteredQueryBuilder(string $keyword): QueryBuilder
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where($queryBuilder->expr()->like('entity.title', ':keyword'))
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws NoResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $queryBuilder = $this->getFilteredQueryBuilder($keyword);

        return $queryBuilder
            ->select($queryBuilder->expr()->count('entity'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
