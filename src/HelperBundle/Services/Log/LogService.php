<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:27
 */

namespace HelperBundle\Services\Log;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\OrderBy;
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
    public function getAll(int $offset = 0, int $count = 20, $sortBy = 'createdAt', $sortOrder = 'desc', $filter = ''): array
    {
        $repo = $this->entityManager->getRepository(LogEntry::class);
        $query = $repo->createQueryBuilder('log_entry')
            ->where("log_entry.message like :filter")
            ->orderBy(new OrderBy('log_entry' . $sortBy, $sortOrder))
            ->setParameter('filter', $filter)
            ->setMaxResults($count)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @inheritdoc
     */
    public function get(int $id): LogEntry
    {
        return $this->entityManager->find(LogEntry::class, $id);
    }
}