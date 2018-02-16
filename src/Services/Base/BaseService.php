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

abstract class BaseService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var EntityRepository */
    private $repository;
    /** @var string */
    private $entityType;

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
        $entity = $this->getById($id);
        $entity->{"set$key"}($value);

        $this->save($entity);
    }

    /**
     * Gets the entity by id
     *
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getById(int $id)
    {
        return $this->entityManager->find($this->entityType, $id);
    }

    /**
     * Saves the given entity
     *
     * @param $item
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function save($item)
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
     * Deletes the entity with the given id
     *
     * @param int $id
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(int $id)
    {
        $item = $this->getById($id);
        $this->entityManager->remove($item);
        $this->entityManager->flush();
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
}