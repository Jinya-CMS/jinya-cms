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
     * @param Menu $menu
     * @param MenuItem $item
     * @return Menu
     */
    public function addItem(Menu $menu, MenuItem $item): Menu;

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
     * Removes the given @see MenuItem
     *
     * @param MenuItem $item
     */
    public function removeItem(MenuItem $item): void;

    /**
     * Updates the given @see MenuItem
     *
     * @param MenuItem $item
     * @return MenuItem
     */
    public function updateItem(MenuItem $item): MenuItem;
}