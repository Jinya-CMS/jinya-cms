<?php

namespace App\Theming\Extensions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use JetBrains\PhpStorm\Pure;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

/**
 * Provides extensions to the Plates engine, adding helper methods for menus
 */
class MenuExtension implements ExtensionInterface
{

    /**
     * Registers the helper method with the plates engine
     * 
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
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
        return $menuItem->route === ltrim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Gets the active menu item
     *
     * @return MenuItem|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    public function getActiveMenuItem(): ?MenuItem
    {
        return MenuItem::findByRoute(ltrim($_SERVER['REQUEST_URI'], '/'));
    }
}