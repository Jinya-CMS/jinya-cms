<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:27
 */

namespace HelperBundle\Services\Log;


use Doctrine\ORM\EntityManager;
use HelperBundle\Entity\LogEntry;

class LogService implements LogServiceInterface
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * LogService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function getAll(int $offset = 0, int $count = 20, $sortBy = 'createdAt', $sortOrder = 'desc', $level = 'info', $filter = ''): array
    {
        $queryBuilder = $this->getFilterQueryBuilder($level, $filter)
            ->setMaxResults($count)
            ->setFirstResult($offset);
        if ($sortOrder === 'asc') {
            $queryBuilder = $queryBuilder->orderBy($queryBuilder->expr()->asc('le.' . $sortBy));
        } else {
            $queryBuilder = $queryBuilder->orderBy($queryBuilder->expr()->desc('le.' . $sortBy));
        }
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    private function getFilterQueryBuilder(string $level, string $filter)
    {
        $queryBuilder = $this->entityManager->getRepository(LogEntry::class)->createQueryBuilder('le');

        return $queryBuilder
            ->where($queryBuilder->expr()->like('le.message', ':filter'))
            ->andWhere($queryBuilder->expr()->like('le.levelName', $queryBuilder->expr()->upper(':level')))
            ->setParameter('filter', "%$filter%")
            ->setParameter('level', "%$level%");
    }

    /**
     * @inheritdoc
     */
    public function get(int $id): LogEntry
    {
        return $this->entityManager->find(LogEntry::class, $id);
    }

    /**
     * Counts all elements
     * @return int
     */
    public function countAll(): int
    {
        $queryBuilder = $this->entityManager->getRepository(LogEntry::class)->createQueryBuilder('le');
        return $queryBuilder
            ->select($queryBuilder->expr()->count('le'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Counts all elements based on the filters
     *
     * @param string $level
     * @param string $filter
     * @return int
     */
    public function countFiltered(string $level, string $filter): int
    {
        $queryBuilder = $this->getFilterQueryBuilder($level, $filter);
        return $queryBuilder
            ->select($queryBuilder->expr()->count('le'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Gets a list of all used log levels
     *
     * @return array
     */
    public function getUsedLevels(): array
    {
        $queryBuilder = $this->entityManager
            ->getRepository(LogEntry::class)
            ->createQueryBuilder('le')
            ->select(['le.level', 'le.levelName'])
            ->distinct(true);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}