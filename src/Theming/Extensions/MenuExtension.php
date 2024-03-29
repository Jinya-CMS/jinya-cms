<?php

namespace App\Theming\Extensions;

use App\Database\MenuItem;
use JetBrains\PhpStorm\Pure;
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\BaseExtension;

/**
 * Provides extensions to the Plates engine, adding helper methods for menus
 */
class MenuExtension extends BaseExtension
{
    /**
     * Registers the helper method with the plates engine
     *
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
    {
        $engine->functions->add('getActiveMenuItem', [$this, 'getActiveMenuItem']);
        $engine->functions->add('isActiveMenuItem', [$this, 'isActiveMenuItem']);
        $engine->functions->add('isChildActiveMenuItem', [$this, 'isChildActiveMenuItem']);
    }

    /**
     * Checks if the given menu item is active or a child is active
     *
     * @param MenuItem $menuItem
     * @return bool
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
     */
    public function getActiveMenuItem(): ?MenuItem
    {
        return MenuItem::findByRoute(ltrim($_SERVER['REQUEST_URI'], '/'));
    }
}
