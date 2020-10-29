<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 19:00
 */

namespace Jinya\Services\Menu;

use Jinya\Entity\Menu\MenuItem;

interface MenuItemServiceInterface
{
    public const MENU = 'menu';

    public const PARENT = 'parent';

    /**
     * Gets the all menu items for the given menu
     */
    public function getAll(int $parentId, string $type = self::PARENT): array;

    /**
     * Gets the menu item by position and parent id
     */
    public function get(int $parentId, int $position, string $type = self::PARENT): MenuItem;

    /**
     * Adds the given menu item
     */
    public function addItem(int $parentId, MenuItem $item, string $type = self::PARENT): void;

    /**
     * Removes the given menu item from the menu
     */
    public function removeItem(int $parentId, int $position, string $type = self::PARENT): void;

    /**
     * Updates the given @param MenuItem $item
     * @see MenuItem
     */
    public function updateItem(MenuItem $item): MenuItem;
}
