<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;

#[OpenApiModel('A theme menu represents a menu in the current theme configuration')]
class ThemeMenu extends ThemeHelperEntity implements FormattableEntityInterface
{

    #[OpenApiField(object: true, objectRef: Menu::class, name: 'menu')]
    public int $menuId = -1;

    /**
     * Finds a menu by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeMenu|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeMenu
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_menu', new self());
    }

    /**
     * Finds the menus for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_menu', new self());
    }

    #[ArrayShape(['name' => "string", 'menu' => "array"])] public function format(): array
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
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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