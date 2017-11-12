<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 17:00
 */

namespace DataBundle\Services\Galleries;

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
     * Saves the given entity
     *
     * @param $item
     * @return mixed
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

    public abstract function getById(int $id);

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