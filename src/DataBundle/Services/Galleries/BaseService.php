<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 17:00
 */

namespace DataBundle\Services\Galleries;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

abstract class BaseService
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var TokenStorage */
    private $tokenStorage;

    /**
     * BaseService constructor.
     * @param EntityManager $entityManager
     * @param TokenStorage $tokenStorage
     */
    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Saves the given entity
     *
     * @param $item
     * @return mixed
     */
    protected function save($item)
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();
        if ($item->getId() !== null) {
            $entity = $this->getById($item->getId());
            $item = $this->mergeEntities($entity, $item);
        } else {
            $item->setCreator($currentUser);
        }
        $item->setUpdatedBy($currentUser);

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