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
    private EntityManagerInterface $entityManager;

    private MenuServiceInterface $menuService;

    private EventDispatcherInterface $eventDispatcher;

    /**
     * MenuItemService constructor.
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
     */
    public function getAll(int $parentId, string $type = MenuItemServiceInterface::PARENT): array
    {
        $this->eventDispatcher->dispatch(
            new MenuItemGetAllEvent($parentId, $type, []),
            MenuItemGetAllEvent::PRE_GET_ALL
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
            new MenuItemGetAllEvent($parentId, $type, $items),
            MenuItemGetAllEvent::POST_GET_ALL
        );

        return $items;
    }

    /**
     * Gets the menu item by position and parent id
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function get(int $parentId, int $position, string $type = MenuItemServiceInterface::PARENT): MenuItem
    {
        $this->eventDispatcher->dispatch(
            new MenuItemGetEvent($parentId, $type, $position, null),
            MenuItemGetEvent::PRE_GET
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
            new MenuItemGetEvent($parentId, $type, $position, $item),
            MenuItemGetEvent::POST_GET
        );

        return $item;
    }

    /**
     * Adds the given menu item
     */
    public function addItem(int $parentId, MenuItem $item, string $type = MenuItemServiceInterface::PARENT): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new MenuItemAddEvent($parentId, $type, $item->getPosition(), $item),
            MenuItemAddEvent::PRE_ADD
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
                new MenuItemAddEvent($parentId, $type, $item->getPosition(), $item),
                MenuItemAddEvent::POST_ADD
            );
        }
    }

    private function rearrangeMenuItems(
        int $position,
        int $parentId,
        string $type = MenuItemServiceInterface::PARENT
    ): int {
        if (MenuItemServiceInterface::MENU === $type) {
            $positions = $this->menuService->get($parentId)->getMenuItems()->toArray();
        } else {
            /** @noinspection NullPointerExceptionInspection */
            $positions = $this->entityManager->find(MenuItem::class, $parentId)->getChildren()->toArray();
        }

        uasort($positions, static function ($a, $b) {
            /* @var MenuItem $a */
            /* @var MenuItem $b */
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        $positions = array_values($positions);

        $newPosition = $position;

        if (-1 === $newPosition) {
            $newPosition = array_shift($positions)->getPosition() + 1;
        }

        /** @var MenuItem $menuItem */
        foreach ($positions as $key => $menuItem) {
            $menuItem->setPosition($key);
        }

        foreach ($positions as $menuItem) {
            if ($menuItem->getPosition() >= $newPosition) {
                $menuItem->setPosition($menuItem->getPosition() + 1);
            }
        }

        return $newPosition;
    }

    /**
     * Removes the given @param int $parentId
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
            new MenuItemRemoveEvent($parentId, $type, $position, $item),
            MenuItemRemoveEvent::PRE_REMOVE
        );

        if (!$pre->isCancel()) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                new MenuItemRemoveEvent($parentId, $type, $position, $item),
                MenuItemRemoveEvent::POST_REMOVE
            );
        }
    }

    /**
     * Updates the given @param MenuItem $item
     * @see MenuItem
     */
    public function updateItem(MenuItem $item): MenuItem
    {
        $pre = $this->eventDispatcher->dispatch(new MenuItemUpdateEvent($item), MenuItemUpdateEvent::PRE_UPDATE);

        if (!$pre->isCancel()) {
            /** @noinspection NullPointerExceptionInspection */
            $parentId = $item->getParent() ? $item->getParent()->getId() : $item->getMenu()->getId();
            $position = $this->rearrangeMenuItems(
                $item->getPosition(),
                $parentId,
                $item->getParent() ? MenuItemServiceInterface::PARENT : MenuItemServiceInterface::MENU
            );
            $this->entityManager->flush();

            $item->setPosition($position);

            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new MenuItemUpdateEvent($item), MenuItemUpdateEvent::POST_UPDATE);
        }

        return $item;
    }
}
