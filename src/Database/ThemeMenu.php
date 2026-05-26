<?php

namespace Jinya\Cms\Database;

use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Creatable;
use Jinya\Database\Deletable;
use Jinya\Database\DeletableEntityTrait;
use Jinya\Database\EntityTrait;
use Jinya\Database\Updatable;

/**
 * This class contains a menu connected to a theme
 */
#[Table('theme_menu')]
class ThemeMenu implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;
    use ThemeLinkTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The menu ID */
    #[Column(sqlName: 'menu_id')]
    public int $menuId = -1;

    private static function getLinkIdProperty(): string
    {
        return 'menuId';
    }

    private static function getLinkIdColumn(): string
    {
        return 'menu_id';
    }


    /**
     * Formats the theme menu into an array
     *
     * @return array<string, array<string, array<string, int|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'menu' => 'array'])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'menu' => $this->getMenu()?->format(),
        ];
    }

    /**
     * Gets the menu of the theme menu
     *
     * @return Menu|null
     */
    public function getMenu(): ?Menu
    {
        return Menu::findById($this->menuId);
    }
}
