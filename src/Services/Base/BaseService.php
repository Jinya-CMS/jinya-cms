<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 17:00
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

class BaseService
{
    protected EntityManagerInterface $entityManager;

    protected string $entityType;

    /**
     * BaseService constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, string $entityType)
    {
        $this->entityManager = $entityManager;
        $this->entityType = $entityType;
    }

    /**
     * Saves the given entity
     *
     * @param mixed $entity
     * @return mixed
     */
    public function saveOrUpdate($entity)
    {
        $state = $this->entityManager->getUnitOfWork()->getEntityState($entity);
        if (UnitOfWork::STATE_MANAGED !== $state && UnitOfWork::STATE_REMOVED !== $state) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        return $entity;
    }

    /**
     * Deletes the given Entity
     *
     * @param $entity
     */
    public function delete($entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * Gets a @return QueryBuilder
     * @see QueryBuilder for the current entity type
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->select('entity')->from($this->entityType, 'entity');
    }
}
