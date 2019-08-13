<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 19:04
 */

namespace Jinya\Services\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Jinya\Entity\Menu\MenuItem;
use Jinya\Framework\Events\Menu\MenuItemAddEvent;
use Jinya\Framework\Events\Menu\MenuItemGetAllEvent;
use Jinya\Framework\Events\Menu\MenuItemGetEvent;
use Jinya\Framework\Events\Menu\MenuItemRemoveEvent;
use Jinya\Framework\Events\Menu\MenuItemUpdateEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuItemService implements MenuItemServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MenuServiceInterface */
    private $menuService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * MenuItemService constructor.
     * @param EntityManagerInterface $entityManager
     * @param MenuServiceInterface $menuService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MenuServiceInterface $menuService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->menuService = $menuService;
        $this->eventDispatcher = $eventDispatcher;
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
        $this->eventDispatcher->dispatch(
            MenuItemGetAllEvent::PRE_GET_ALL,
            new MenuItemGetAllEvent($parentId, $type, [])
        );
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(MenuItem::class, 'item')
            ->setParameter('parentId', $parentId);

        if (MenuItemServiceInterface::MENU === $type) {
            $queryBuilder
                ->where('menu.id = :parentId')
                ->join('item.menu', 'menu');
        } else {
            $queryBuilder
                ->where('parent.id = :parentId')
                ->join('item.parent', 'parent');
        }

        $items = $queryBuilder
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(
            MenuItemGetAllEvent::POST_GET_ALL,
            new MenuItemGetAllEvent($parentId, $type, $items)
        );

        return $items;
    }

    /**
     * Gets the menu item by position and parent id
     *
     * @param int $parentId
     * @param int $position
     * @param string $type
     * @return MenuItem
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function get(int $parentId, int $position, string $type = MenuItemServiceInterface::PARENT): MenuItem
    {
        $this->eventDispatcher->dispatch(
            MenuItemGetEvent::PRE_GET,
            new MenuItemGetEvent($parentId, $type, $position, null)
        );

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(MenuItem::class, 'item')
            ->where('item.position = :position');

        if (MenuItemServiceInterface::MENU === $type) {
            $queryBuilder = $queryBuilder
                ->andWhere('menu.id = :parentId')
                ->join('item.menu', 'menu');
        } else {
            $queryBuilder = $queryBuilder
                ->andWhere('parent.id = :parentId')
                ->join('item.parent', 'parent');
        }

        $item = $queryBuilder
            ->setParameter('parentId', $parentId)
            ->setParameter('position', $position)
            ->getQuery()
            ->getSingleResult();

        $this->eventDispatcher->dispatch(
            MenuItemGetEvent::POST_GET,
            new MenuItemGetEvent($parentId, $type, $position, $item)
        );

        return $item;
    }

    /**
     * Adds the given menu item
     *
     * @param int $parentId
     * @param MenuItem $item
     * @param string $type
     */
    public function addItem(int $parentId, MenuItem $item, string $type = MenuItemServiceInterface::PARENT): void
    {
        $pre = $this->eventDispatcher->dispatch(
            MenuItemAddEvent::PRE_ADD,
            new MenuItemAddEvent($parentId, $type, $item->getPosition(), $item)
        );

        if (!$pre->isCancel()) {
            $position = $this->rearrangeMenuItems($item->getPosition(), $parentId, $type);
            $this->entityManager->flush();

            $item->setPosition($position);

            if (MenuItemServiceInterface::MENU === $type) {
                $menu = $this->menuService->get($parentId);
                $item->setMenu($menu);
            } else {
                $parent = $this->entityManager->find(MenuItem::class, $parentId);
                $item->setParent($parent);
            }

            $this->entityManager->persist($item);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(
                MenuItemAddEvent::POST_ADD,
                new MenuItemAddEvent($parentId, $type, $item->getPosition(), $item)
            );
        }
    }

    /**
     * @param int $position
     * @param int $parentId
     * @param string $type
     * @return int
     */
    private function rearrangeMenuItems(
        int $position,
        int $parentId,
        string $type = MenuItemServiceInterface::PARENT
    ): int {
        if (MenuItemServiceInterface::MENU === $type) {
            $positions = $this->menuService->get($parentId)->getMenuItems()->toArray();
        } else {
            $positions = $this->entityManager->find(MenuItem::class, $parentId)->getChildren()->toArray();
        }

        uasort($positions, function ($a, $b) {
            /* @var MenuItem $a */
            /* @var MenuItem $b */
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
     * Removes the given @param int $parentId
     * @param int $position
     * @param string $type
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @see MenuItem
     */
    public function removeItem(int $parentId, int $position, string $type = MenuItemServiceInterface::PARENT): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(MenuItem::class, 'item')
            ->where('item.position = :position');

        if (MenuItemServiceInterface::MENU === $type) {
            $queryBuilder = $queryBuilder->join('item.menu', 'parent');
        } else {
            $queryBuilder = $queryBuilder->join('item.parent', 'parent');
        }

        $item = $queryBuilder
            ->andWhere('parent.id = :parentId')
            ->setParameter('position', $position)
            ->setParameter('parentId', $parentId)
            ->getQuery()
            ->getSingleResult();

        $pre = $this->eventDispatcher->dispatch(
            MenuItemRemoveEvent::PRE_REMOVE,
            new MenuItemRemoveEvent($parentId, $type, $position, $item)
        );

        if (!$pre->isCancel()) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                MenuItemRemoveEvent::POST_REMOVE,
                new MenuItemRemoveEvent($parentId, $type, $position, $item)
            );
        }
    }

    /**
     * Updates the given @param MenuItem $item
     * @return MenuItem
     * @see MenuItem
     */
    public function updateItem(MenuItem $item): MenuItem
    {
        $pre = $this->eventDispatcher->dispatch(MenuItemUpdateEvent::PRE_UPDATE, new MenuItemUpdateEvent($item));

        if (!$pre->isCancel()) {
            $parentId = $item->getParent() ? $item->getParent()->getId() : $item->getMenu()->getId();
            $position = $this->rearrangeMenuItems(
                $item->getPosition(),
                $parentId,
                $item->getParent() ? MenuItemServiceInterface::PARENT : MenuItemServiceInterface::MENU
            );
            $this->entityManager->flush();

            $item->setPosition($position);

            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(MenuItemUpdateEvent::POST_UPDATE, new MenuItemUpdateEvent($item));
        }

        return $item;
    }
}
