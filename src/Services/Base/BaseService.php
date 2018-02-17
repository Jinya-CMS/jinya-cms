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
use Jinya\Entity\BaseEntity;

trait BaseService
{
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var EntityRepository */
    private $repository;
    /** @var string */
    private $entityType;

    /**
     * @inheritdoc
     */
    public function updateField(string $key, string $value, int $id)
    {
        $entity = $this->getById($id);
        $entity->{"set$key"}($value);

        $this->saveOrUpdate($entity);
    }

    /**
     * Gets the entity by id
     *
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getById(int $id)
    {
        return $this->getQueryBuilder()
            ->where('entity.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Gets a @see QueryBuilder for the current entity type
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        if ($this->repository === null) {
            $this->repository = $this->entityManager->getRepository($this->entityType);
        }

        return $this->repository->createQueryBuilder('entity');
    }

    /**
     * Saves the given entity
     *
     * @param $item
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveOrUpdate(BaseEntity $item)
    {
        if ($item->getId() !== null) {
            $entity = $this->getById($item->getId());
            $item = $this->mergeEntities($entity, $item);

            $item = $this->entityManager->merge($item);
        } else {
            $this->entityManager->persist($item);
        }

        $this->entityManager->flush();

        return $item;
    }

    /**
     * Merges the set properties of new into original and returns original
     *
     * @param $original
     * @param $new
     * @return mixed
     */
    protected function mergeEntities($original, $new)
    {
        $newAsArray = json_decode(json_encode($new), true);
        $originalAsArray = json_decode(json_encode($original), true);
        foreach ($originalAsArray as $key => $item) {
            if (isset($newAsArray[$key])) {
                if (method_exists($original, "set$key")) {
                    $original->{"set$key"}($new->{"get$key"}());
                }
            }
        }

        return $original;
    }

    /**
     * Deletes the given @see BaseEntity
     *
     * @param BaseEntity $entity
     * @return void
     */
    public function delete(BaseEntity $entity): void
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
            } else {
                $fields[$key] = "entity.history = ''";
            }
        }
        return $fields;
    }
}