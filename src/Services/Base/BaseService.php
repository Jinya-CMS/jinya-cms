<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 17:00
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Jinya\Entity\BaseEntity;

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

    /**
     * @inheritdoc
     */
    public function updateField(string $key, string $value, int $id)
    {
        $entity = $this->getRepository()->find($id);
        $entity->{"set$key"}($value);

        $this->saveOrUpdate($entity);
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->entityType);
    }

    /**
     * Saves the given entity
     *
     * @param BaseEntity $entity
     * @return BaseEntity
     */
    public function saveOrUpdate($entity)
    {
        if ($this->entityManager->getUnitOfWork()->getEntityState($entity) === UnitOfWork::STATE_NEW) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        return $entity;
    }

    /**
     * Deletes the given @see BaseEntity
     *
     * @param BaseEntity $entity
     * @return void
     */
    public function delete($entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * Gets all fields excluding the clutter, like history
     *
     * @return array
     */
    protected function getFieldsWithoutClutter(): array
    {
        $fieldsInEntity = $this->entityManager->getClassMetadata($this->entityType)->getFieldNames();
        $fields = [];

        foreach ($fieldsInEntity as $key => $field) {
            if ($field !== 'history') {
                $fields[$key] = "entity.$field";
            }
        }
        return $fields;
    }

    /**
     * Gets a @see QueryBuilder for the current entity type
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->getRepository()->createQueryBuilder('entity');
    }
}