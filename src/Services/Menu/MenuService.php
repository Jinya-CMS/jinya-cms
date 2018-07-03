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

class MenuService implements MenuServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * MenuService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Menu $menu): Menu
    {
        if (null === $menu->getId()) {
            $this->entityManager->persist($menu);
        }

        $this->entityManager->flush();

        return $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Menu::class)->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): void
    {
        $menu = $this->get($id);
        $this->entityManager->remove($menu);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $id): Menu
    {
        return $this->entityManager->find(Menu::class, $id);
    }

    /**
     * Fills the menu items from the given array
     *
     * @param int $id
     * @param array $data
     */
    public function fillFromArray(int $id, array $data): void
    {
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
