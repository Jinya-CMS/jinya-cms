<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:27
 */

namespace Jinya\Services\Log;

use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Jinya\Entity\Logging\LogEntry;
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
     * {@inheritdoc}
     */
    public function getAll(
        int $offset = 0,
        int $count = 20,
        $sortBy = 'createdAt',
        $sortOrder = 'desc',
        $level = 'info',
        $filter = ''
    ): array {
        $queryBuilder = $this->getFilterQueryBuilder($level, $filter)
            ->setMaxResults($count)
            ->setFirstResult($offset);
        if ('asc' === $sortOrder) {
            $queryBuilder = $queryBuilder->orderBy(':sortBy', 'asc');
        } else {
            $queryBuilder = $queryBuilder->orderBy(':sortBy', 'desc');
        }
        $query = $queryBuilder->setParameter('sortBy', $sortBy)->getQuery();

        return $query->getResult();
    }

    /**
     * Gets a @param string $level
     * @param string $filter
     * @return QueryBuilder
     * @see QueryBuilder filtered by level and filter
     */
    private function getFilterQueryBuilder(string $level, string $filter)
    {
        $uppercaseLevel = strtoupper($level);

        return $this->createQueryBuilder()
            ->where('le.message LIKE :filter')
            ->andWhere('le.levelName LIKE :level')
            ->setParameter('filter', "%$filter%")
            ->setParameter('level', "%$uppercaseLevel%");
    }

    /**
     * @return QueryBuilder
     */
    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(LogEntry::class, 'le');
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $id): LogEntry
    {
        return $this->entityManager->find(LogEntry::class, $id);
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
     */
    public function countAll(): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->from(LogEntry::class, 'le')
            ->select($queryBuilder->expr()->count('le'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
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
     * {@inheritdoc}
     */
    public function getUsedLevels(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(LogEntry::class, 'le')
            ->select(['le.level', 'le.levelName'])
            ->distinct(true)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     * @throws ConnectionException
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
        } catch (Exception $exception) {
            $connection->rollBack();
            $this->logger->error('Could not clear database log');
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
        }

        $fs = new Filesystem();
        /* @noinspection PhpUndefinedMethodInspection */
        foreach ($this->logger->getHandlers() as $handler) {
            try {
                if ($handler instanceof StreamHandler || $handler instanceof RotatingFileHandler) {
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
