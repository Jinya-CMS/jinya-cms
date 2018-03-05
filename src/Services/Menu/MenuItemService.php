<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 19:04
 */

namespace Jinya\Services\Menu;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\MenuItem;

class MenuItemService implements MenuItemServiceInterface
{

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var MenuServiceInterface */
    private $menuService;

    /**
     * MenuItemService constructor.
     * @param EntityManagerInterface $entityManager
     * @param MenuServiceInterface $menuService
     */
    public function __construct(EntityManagerInterface $entityManager, MenuServiceInterface $menuService)
    {
        $this->entityManager = $entityManager;
        $this->menuService = $menuService;
    }

    /**
     * Gets the all menu items for the given menu
     *
     * @param int $parentId
     * @param string $type
     * @return array
     */
    public function getAll(int $parentId, string $type = MenuItemServiceInterface::PARENT): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(MenuItem::class, 'item')
            ->setParameter('parentId', $parentId);

        if ($type === MenuItemServiceInterface::MENU) {
            $queryBuilder
                ->where('menu.id = :parentId')
                ->join('item.menu', 'menu');
        } else {
            $queryBuilder
                ->where('parent.id = :parentId')
                ->join('item.parent', 'parent');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets the menu item by position and parent id
     *
     * @param int $parentId
     * @param int $position
     * @param string $type
     * @return MenuItem
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(int $parentId, int $position, string $type = MenuItemServiceInterface::PARENT): MenuItem
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(MenuItem::class, 'item')
            ->where('item.position = :position');

        if ($type === MenuItemServiceInterface::MENU) {
            $queryBuilder = $queryBuilder
                ->andWhere('menu.id = :parentId')
                ->join('item.menu', 'menu');
        } else {
            $queryBuilder = $queryBuilder
                ->andWhere('parent.id = :parentId')
                ->join('item.parent', 'parent');
        }

        return $queryBuilder
            ->setParameter('parentId', $parentId)
            ->setParameter('position', $position)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Adds the given menu item
     *
     * @param int $parentId
     * @param MenuItem $item
     * @param string $type
     * @return void
     */
    public function addItem(int $parentId, MenuItem $item, string $type = MenuItemServiceInterface::PARENT): void
    {
        $position = $this->rearrangeMenuItems($item->getPosition(), $parentId, $type);
        $this->entityManager->flush();

        $item->setPosition($position);

        if ($type === MenuItemServiceInterface::MENU) {
            $menu = $this->menuService->get($parentId);
            $item->setMenu($menu);
        } else {
            $parent = $this->entityManager->find(MenuItem::class, $parentId);
            $item->setParent($parent);
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    /**
     * @param int $position
     * @param int $parentId
     * @return int
     */
    private function rearrangeMenuItems(int $position, int $parentId, string $type = MenuItemServiceInterface::PARENT): int
    {
        if ($type === MenuItemServiceInterface::MENU) {
            $positions = $this->menuService->get($parentId)->getMenuItems()->toArray();
        } else {
            $positions = $this->entityManager->find(MenuItem::class, $parentId)->getChildren()->toArray();
        }

        uasort($positions, function ($a, $b) {
            /** @var MenuItem $a */
            /** @var MenuItem $b */
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        $positions = array_values($positions);

        if ($position === -1) {
            $position = array_shift($positions)->getPosition() + 1;
        }

        /** @var MenuItem $menuItem */
        foreach ($positions as $key => $menuItem) {
            $menuItem->setPosition($key);
        }

        foreach ($positions as $menuItem) {
            if ($menuItem->getPosition() >= $position) {
                $menuItem->setPosition($menuItem->getPosition() + 1);
            }
        }

        return $position;
    }

    /**
     * Removes the given @see MenuItem
     *
     * @param int $id
     * @param int $position
     * @param string $type
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeItem(int $id, int $position, string $type = MenuItemServiceInterface::PARENT): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(MenuItem::class, 'item')
            ->where('item.position = :position');

        if ($type === MenuItemServiceInterface::MENU) {
            $queryBuilder = $queryBuilder->join('item.menu', 'parent');
        } else {
            $queryBuilder = $queryBuilder->join('item.parent', 'parent');
        }

        $queryBuilder
            ->andWhere('parent.id = :id')
            ->setParameter('position', $position)
            ->setParameter('id', $id);

        $this->entityManager->remove($queryBuilder->getQuery()->getSingleResult());
        $this->entityManager->flush();
    }

    /**
     * Updates the given @see MenuItem
     *
     * @param MenuItem $item
     * @return MenuItem
     */
    public function updateItem(MenuItem $item): MenuItem
    {
        $parentId = $item->getParent() ? $item->getParent()->getId() : $item->getMenu()->getId();
        $position = $this->rearrangeMenuItems($item->getPosition(), $parentId, $item->getParent() ? MenuItemServiceInterface::PARENT : MenuItemServiceInterface::MENU);
        $this->entityManager->flush();

        $item->setPosition($position);

        $this->entityManager->flush();

        return $item;
    }
}