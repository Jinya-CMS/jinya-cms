<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:27
 */

namespace ServiceBundle\Services\Log;


use Doctrine\ORM\EntityManager;
use Exception;
use ServiceBundle\Entity\LogEntry;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;

class LogService implements LogServiceInterface
{
    /** @var EntityManager */
    private $entityManager;

    /** @var Logger */
    private $log;

    /**
     * LogService constructor.
     * @param EntityManager $entityManager
     * @param Logger $log
     */
    public function __construct(EntityManager $entityManager, Logger $log)
    {
        $this->entityManager = $entityManager;
        $this->log = $log;
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

    /**
     * Removes all log entries from the database and deletes the log files
     *
     * @return void
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
            $this->log->error('Could not clear database log');
            $this->log->error($exception->getMessage());
            $this->log->error($exception->getTraceAsString());
        }

        $fs = new Filesystem();
        foreach ($this->log->getHandlers() as $handler) {
            try {
                if ($handler instanceof StreamHandler) {
                    $fs->remove($handler->getUrl());
                } elseif ($handler instanceof RotatingFileHandler) {
                    $fs->remove($handler->getUrl());
                }
            } catch (Exception $exception) {
                $this->log->error('Could not delete log files');
                $this->log->error($exception->getMessage());
                $this->log->error($exception->getTraceAsString());
            }
        }

        $this->log->info('Successfully cleared log');
    }
}