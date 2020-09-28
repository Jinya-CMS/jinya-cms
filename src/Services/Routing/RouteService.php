<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.01.2018
 * Time: 21:34
 */

namespace Jinya\Services\Routing;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Menu\RoutingEntry;

class RouteService implements RouteServiceInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * RouteService constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUrl(string $url): RoutingEntry
    {
        return $this->entityManager->getRepository(RoutingEntry::class)
            ->findOneBy(['url' => $url]);
    }
}
