<?php

namespace App\Theming;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use JetBrains\PhpStorm\Pure;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class MenuExtension implements ExtensionInterface
{

    /**
     * @return void
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('getActiveMenuItem', [$this, 'getActiveMenuItem']);
        $engine->registerFunction('isActiveMenuItem', [$this, 'isActiveMenuItem']);
        $engine->registerFunction('isChildActiveMenuItem', [$this, 'isChildActiveMenuItem']);
    }

    /**
     * Checks if the given menu item is active or a child is active
     *
     * @param MenuItem $menuItem
     * @return bool
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function isChildActiveMenuItem(MenuItem $menuItem): bool
    {
        foreach ($menuItem->getItems() as $item) {
            if ($this->isActiveMenuItem($item) || $this->isChildActiveMenuItem($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given menu item is active
     *
     * @param MenuItem $menuItem
     * @return bool
     */
    #[Pure] public function isActiveMenuItem(MenuItem $menuItem): bool
    {
        return $menuItem->route !== null && $menuItem->route === ltrim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Gets the active menu item
     *
     * @return MenuItem|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function getActiveMenuItem(): ?MenuItem
    {
        return MenuItem::findByRoute(ltrim($_SERVER['REQUEST_URI'], '/'));
    }
}