<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 17:49
 */

namespace Jinya\Formatter\Menu;

use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;
use function array_map;

class MenuFormatter implements MenuFormatterInterface
{
    private array $formattedData;

    private Menu $menu;

    private MenuItemFormatterInterface $menuItemFormatter;

    public function setMenuItemFormatter(MenuItemFormatterInterface $menuItemFormatter): void
    {
        $this->menuItemFormatter = $menuItemFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the @param Menu $menu
     * @see MenuFormatterInterface
     */
    public function init(Menu $menu): MenuFormatterInterface
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Formats the name
     */
    public function name(): MenuFormatterInterface
    {
        $this->formattedData['name'] = $this->menu->getName();

        return $this;
    }

    /**
     * Formats the id
     */
    public function id(): MenuFormatterInterface
    {
        $this->formattedData['id'] = $this->menu->getId();

        return $this;
    }

    /**
     * Formats the logo
     */
    public function logo(): MenuFormatterInterface
    {
        $this->formattedData['logo'] = $this->menu->getLogo();

        return $this;
    }

    /**
     * Formats the items
     */
    public function items(): MenuFormatterInterface
    {
        $this->formattedData['children'] = array_map(function (MenuItem $menuItem) {
            return $this->menuItemFormatter
                ->init($menuItem)
                ->id()
                ->children()
                ->position()
                ->title()
                ->route()
                ->highlighted()
                ->pageType()
                ->format();
        }, $this->menu->getMenuItems()->toArray());

        return $this;
    }
}
