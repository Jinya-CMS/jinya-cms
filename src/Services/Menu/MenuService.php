<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 30.12.2017
 * Time: 23:08
 */

namespace Jinya\Services\Menu;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Menu\MenuDeleteEvent;
use Jinya\Framework\Events\Menu\MenuFillFromArrayEvent;
use Jinya\Framework\Events\Menu\MenuGetEvent;
use Jinya\Framework\Events\Menu\MenuSaveEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuService implements MenuServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * MenuService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function saveOrUpdate(Menu $menu): Menu
    {
        $pre = $this->eventDispatcher->dispatch(MenuSaveEvent::PRE_SAVE, new MenuSaveEvent($menu));

        if (!$pre->isCancel()) {
            if (null === $menu->getId()) {
                $this->entityManager->persist($menu);
            }

            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(MenuSaveEvent::POST_SAVE, new MenuSaveEvent($menu));
        }

        return $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        $this->eventDispatcher->dispatch(ListEvent::MENU_PRE_GET_ALL, new ListEvent(-1, -1, '', []));

        $items = $this->entityManager->getRepository(Menu::class)->findAll();

        $this->eventDispatcher->dispatch(ListEvent::MENU_POST_GET_ALL, new ListEvent(-1, -1, '', $items));

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): void
    {
        $pre = $this->eventDispatcher->dispatch(MenuDeleteEvent::PRE_DELETE, new MenuDeleteEvent($id));

        if (!$pre->isCancel()) {
            $menu = $this->get($id);
            $this->entityManager->remove($menu);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(MenuDeleteEvent::POST_DELETE, new MenuDeleteEvent($id));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $id): Menu
    {
        $this->eventDispatcher->dispatch(MenuGetEvent::PRE_GET, new MenuGetEvent($id, null));
        $menu = $this->entityManager->find(Menu::class, $id);
        $this->eventDispatcher->dispatch(MenuGetEvent::PRE_GET, new MenuGetEvent($id, $menu));

        return $menu;
    }

    /**
     * Fills the menu items from the given array
     *
     * @param int $id
     * @param array $data
     */
    public function fillFromArray(int $id, array $data): void
    {
        $pre = $this->eventDispatcher->dispatch(MenuFillFromArrayEvent::PRE_FILL_FROM_ARRAY, new MenuFillFromArrayEvent($id, $data));
        if (!$pre->isCancel()) {
            $this->entityManager->transactional(function ($em) use ($data, $id) {
                $menu = $this->get($id);

                $menu->setMenuItems(new ArrayCollection());

                $menuItems = [];

                foreach ($data as $key => $item) {
                    if (0 === $item['nestingLevel']) {
                        $tail = array_slice($data, $key + 1, count($data));
                        $menuItem = $this->createSubmenu($item, $tail, $em);
                        $menuItem->setMenu($menu);
                        $menuItems[] = $menuItem;
                    }
                }

                $this->fixPositions($menuItems);
            });

            $this->eventDispatcher->dispatch(MenuFillFromArrayEvent::POST_FILL_FROM_ARRAY, new MenuFillFromArrayEvent($id, $data));
        }
    }

    private function createSubmenu(array $currentItem, array $tail, EntityManagerInterface $entityManager)
    {
        $menuItem = MenuItem::fromArray($currentItem);

        $children = [];

        if (!empty($tail)) {
            $nestingLevel = $currentItem['nestingLevel'];

            foreach ($tail as $key => $item) {
                if ($nestingLevel + 1 === $item['nestingLevel']) {
                    $child = $this->createSubmenu($item, array_slice($tail, $key + 1, count($tail)), $entityManager);
                    $child->setParent($menuItem);
                    $entityManager->persist($child);
                    $children[] = $child;
                } elseif ($nestingLevel >= $item['nestingLevel']) {
                    break;
                }
            }
        }

        $children = $this->fixPositions($children);

        $menuItem->setChildren(new ArrayCollection($children));
        $entityManager->persist($menuItem);

        return $menuItem;
    }

    /**
     * @param array $items
     * @return array
     */
    private function fixPositions(array $items): array
    {
        $positionZero = array_filter($items, function (MenuItem $item) {
            return 0 === $item->getPosition();
        });

        if (count($positionZero) === count($items)) {
            foreach ($items as $idx => $item) {
                /* @var MenuItem $item */
                $item->setPosition($idx);
            }
        }

        return $items;
    }
}
