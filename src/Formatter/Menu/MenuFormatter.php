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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function array_map;

class MenuFormatter implements MenuFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var Menu */
    private $menu;

    /** @var MenuItemFormatterInterface */
    private $menuItemFormatter;

    /** @var UrlGeneratorInterface */
    private $urLGenerator;

    /**
     * MenuFormatter constructor.
     * @param UrlGeneratorInterface $urLGenerator
     */
    public function __construct(UrlGeneratorInterface $urLGenerator)
    {
        $this->urLGenerator = $urLGenerator;
    }

    /**
     * @param MenuItemFormatterInterface $menuItemFormatter
     */
    public function setMenuItemFormatter(MenuItemFormatterInterface $menuItemFormatter): void
    {
        $this->menuItemFormatter = $menuItemFormatter;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the @param Menu $menu
     * @return MenuFormatterInterface
     * @see MenuFormatterInterface
     */
    public function init(Menu $menu): MenuFormatterInterface
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Formats the name
     *
     * @return MenuFormatterInterface
     */
    public function name(): MenuFormatterInterface
    {
        $this->formattedData['name'] = $this->menu->getName();

        return $this;
    }

    /**
     * Formats the id
     *
     * @return MenuFormatterInterface
     */
    public function id(): MenuFormatterInterface
    {
        $this->formattedData['id'] = $this->menu->getId();

        return $this;
    }

    /**
     * Formats the logo
     *
     * @return MenuFormatterInterface
     */
    public function logo(): MenuFormatterInterface
    {
        $this->formattedData['logo'] = $this->urLGenerator->generate('api_menu_logo_get', ['id' => $this->menu->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this;
    }

    /**
     * Formats the items
     *
     * @return MenuFormatterInterface
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
