<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.12.2017
 * Time: 17:14
 */

namespace Jinya\Services\Menu;


use Jinya\Entity\Menu;
use Jinya\Entity\MenuItem;

interface MenuServiceInterface
{

    /**
     * @param Menu $menu
     * @return Menu
     */
    public function save(Menu $menu): Menu;

    /**
     * @param int $id
     * @return Menu
     */
    public function get(int $id): Menu;

    /**
     * Gets all @see MenuItem
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Deletes the @see Menu with the given id
     *
     * @param int $id
     */
    public function delete(int $id): void;

    /**
     * Fills the menu items from the given array
     *
     * @param int $id
     * @param array $data
     */
    public function fillFromArray(int $id, array $data): void;
}