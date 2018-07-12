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
use Jinya\Entity\Base\BaseEntity;

class BaseService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var string */
    protected $entityType;

    /**
     * BaseService constructor.
     * @param EntityManagerInterface $entityManager
     * @param string $entityType
     */
    public function __construct(EntityManagerInterface $entityManager, string $entityType)
    {
        $this->entityManager = $entityManager;
        $this->entityType = $entityType;
    }

    public function updateField(string $key, string $value, int $id)
    {
        $entity = $this->entityManager->find($this->entityType, $id);
        $entity->{"set$key"}($value);

        /* @noinspection PhpParamsInspection */
        $this->saveOrUpdate($entity);
    }

    /**
     * Saves the given entity
     *
     * @param \Jinya\Entity\Base\BaseEntity $entity
     * @return BaseEntity
     */
    public function saveOrUpdate($entity)
    {
        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($entity)) {
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
     * Gets a @see QueryBuilder for the current entity type
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->select('entity')->from($this->entityType, 'entity');
    }
}
