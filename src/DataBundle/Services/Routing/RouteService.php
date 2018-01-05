<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.01.2018
 * Time: 21:34
 */

namespace DataBundle\Services\Routing;


use DataBundle\Entity\RoutingEntry;
use Doctrine\ORM\EntityManager;

class RouteService implements RouteServiceInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RouteService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function findByUrl(string $url): RoutingEntry
    {
        return $this->entityManager->getRepository(RoutingEntry::class)
            ->findOneBy(['url' => $url]);
    }
}