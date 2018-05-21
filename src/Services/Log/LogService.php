<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:27
 */

namespace Jinya\Services\Log;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Jinya\Entity\LogEntry;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class LogService implements LogServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * LogService constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $log
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $log)
    {
        $this->entityManager = $entityManager;
        $this->logger = $log;
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

    /**
     * Gets a @see QueryBuilder filtered by level and filter
     *
     * @param string $level
     * @param string $filter
     * @return \Doctrine\ORM\QueryBuilder
     */
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $truncate = $connection->getDatabasePlatform()->getTruncateTableSQL($this->entityManager->getClassMetadata(LogEntry::class)->getTableName());
            $connection->executeUpdate($truncate);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
            $repository = $this->entityManager->getRepository(LogEntry::class);
            $repository->clear();
        } catch (Exception $exception) {
            $connection->rollBack();
            $this->logger->error('Could not clear database log');
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
        }

        $fs = new Filesystem();
        foreach ($this->logger->getHandlers() as $handler) {
            try {
                if ($handler instanceof StreamHandler) {
                    $fs->remove($handler->getUrl());
                } elseif ($handler instanceof RotatingFileHandler) {
                    $fs->remove($handler->getUrl());
                }
            } catch (Exception $exception) {
                $this->logger->error('Could not delete log files');
                $this->logger->error($exception->getMessage());
                $this->logger->error($exception->getTraceAsString());
            }
        }

        $this->logger->info('Successfully cleared log');
    }
}