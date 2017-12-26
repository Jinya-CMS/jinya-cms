<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 17:00
 */

namespace DataBundle\Services\Base;

use Doctrine\ORM\EntityManager;

abstract class BaseService
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * BaseService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param int $id
     * @return mixed
     */
    public abstract function getById(int $id);

    /**
     * Saves the given entity
     *
     * @param $item
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function save($item)
    {
        if ($item->getId() !== null) {
            $entity = $this->getById($item->getId());
            $item = $this->mergeEntities($entity, $item);
        }

        $item = $this->entityManager->merge($item);
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
}