<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:38
 */

namespace Jinya\Services\Log;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\AccessLogEntry;

class AccessLogService implements AccessLogServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * AccessLogService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @inheritdoc
     */
    public function getAll(int $offset = 0, int $count = 20, string $sortBy = 'createdAt', string $sortOrder = 'DESC'): array
    {
        return $this->entityManager->getRepository(AccessLogEntry::class)->findBy([], [$sortBy => $sortOrder], $count, $offset);
    }

    /**
     * @inheritdoc
     */
    public function get(int $id): AccessLogEntry
    {
        return $this->entityManager->find(AccessLogEntry::class, $id);
    }

    /**
     * @inheritdoc
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(): int
    {
        $queryBuilder = $this->entityManager->getRepository(AccessLogEntry::class)->createQueryBuilder('ale');
        return $queryBuilder
            ->select($queryBuilder->expr()->count('ale'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}