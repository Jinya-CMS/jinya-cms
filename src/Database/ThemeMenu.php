<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;

class ThemeMenu extends ThemeHelperEntity implements FormattableEntityInterface
{

    public string $name;
    public int $menuId;
    public int $themeId;

    /**
     * Finds a menu by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeMenu
     */
    public static function findByThemeAndName(int $themeId, string $name): ThemeMenu
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_menu', new self());
    }

    /**
     * Finds the menus for the given theme
     *
     * @param int $themeId
     * @return Iterator
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_menu', new self());
    }

    public function format(): array
    {
        return [
            'name' => $this->name,
            'menu' => $this->getMenu()->format(),
        ];
    }

    /**
     * Gets the menu of the theme menu
     *
     * @return Menu
     */
    public function getMenu(): Menu
    {
        return Menu::findById($this->menuId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('theme_menu');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_menu');
    }

    /**
     * @inheritDoc
     * @throws Exceptions\UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_menu');
    }
}