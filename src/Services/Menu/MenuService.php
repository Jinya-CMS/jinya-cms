<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 30.12.2017
 * Time: 23:08
 */

namespace Jinya\Services\Menu;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Menu;
use Jinya\Entity\MenuItem;

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
     * @inheritdoc
     */
    public function save(Menu $menu): Menu
    {
        if ($menu->getId() === null) {
            $this->entityManager->persist($menu);
        }

        $this->entityManager->flush();

        return $menu;
    }

    /**
     * @inheritdoc
     */
    public function addItem(Menu $menu, MenuItem $item): Menu
    {
        $menu->getMenuItems()->add($item);
        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $menu;
    }

    /**
     * @inheritdoc
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Menu::class)->findAll();
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id): void
    {
        $menu = $this->get($id);
        $this->entityManager->remove($menu);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function get(int $id): Menu
    {
        return $this->entityManager->find(Menu::class, $id);
    }

    /**
     * @inheritdoc
     */
    public function removeItem(MenuItem $item): void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function updateItem(MenuItem $item): MenuItem
    {
        /** @var MenuItem $item */
        $item = $this->entityManager->merge($item);
        $this->entityManager->flush();

        return $item;
    }
}