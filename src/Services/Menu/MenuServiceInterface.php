<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.12.2017
 * Time: 17:14
 */

namespace Jinya\Services\Menu;

use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;

interface MenuServiceInterface
{
    public function saveOrUpdate(Menu $menu): Menu;

    public function get(int $id): Menu;

    /**
     * Gets all @return array
     * @see MenuItem
     */
    public function getAll(): array;

    /**
     * Deletes the @param int $id
     * @see Menu with the given id
     */
    public function delete(int $id): void;

    /**
     * Fills the menu items from the given array
     */
    public function fillFromArray(int $id, array $data): void;
}
