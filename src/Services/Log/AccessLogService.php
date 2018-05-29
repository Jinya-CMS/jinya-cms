<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:38
 */

namespace Jinya\Services\Log;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Jinya\Entity\AccessLogEntry;
use Psr\Log\LoggerInterface;

class AccessLogService implements AccessLogServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * AccessLogService constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(int $offset = 0, int $count = 20, string $sortBy = 'createdAt', string $sortOrder = 'DESC'): array
    {
        return $this->entityManager->getRepository(AccessLogEntry::class)->findBy([], [$sortBy => $sortOrder], $count, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $id): AccessLogEntry
    {
        return $this->entityManager->find(AccessLogEntry::class, $id);
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->entityManager->getRepository(AccessLogEntry::class)->createQueryBuilder('ale');

        return $queryBuilder
            ->select($queryBuilder->expr()->count('ale'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $truncate = $connection->getDatabasePlatform()->getTruncateTableSQL($this->entityManager->getClassMetadata(AccessLogEntry::class)->getTableName());
            $connection->executeUpdate($truncate);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
            $repository = $this->entityManager->getRepository(AccessLogEntry::class);
            $repository->clear();
        } catch (Exception $exception) {
            $connection->rollBack();
            $this->logger->error('Could not clear access log');
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
        }
    }
}
