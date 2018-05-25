<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:24.
 */

namespace Jinya\Formatter\Menu;

use Jinya\Entity\MenuItem;
use Jinya\Formatter\Route\RouteFormatterInterface;

class MenuItemFormatter implements MenuItemFormatterInterface
{
    /** @var array */
    private $formattedData;
    /** @var MenuItem */
    private $menuItem;

    /** @var MenuFormatterInterface */
    private $menuFormatter;
    /** @var RouteFormatterInterface */
    private $routeFormatter;

    /**
     * Formats the content of the @see FormatterInterface into an array.
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Formats the id.
     *
     * @return MenuItemFormatterInterface
     */
    public function id(): MenuItemFormatterInterface
    {
        $this->formattedData['id'] = $this->menuItem->getId();

        return $this;
    }

    /**
     * Formats the title.
     *
     * @return MenuItemFormatterInterface
     */
    public function title(): MenuItemFormatterInterface
    {
        $this->formattedData['title'] = $this->menuItem->getTitle();

        return $this;
    }

    /**
     * Formats the page type.
     *
     * @return MenuItemFormatterInterface
     */
    public function pageType(): MenuItemFormatterInterface
    {
        $this->formattedData['pageType'] = $this->menuItem->getPageType();

        return $this;
    }

    /**
     * Formats the highlight state.
     *
     * @return MenuItemFormatterInterface
     */
    public function highlighted(): MenuItemFormatterInterface
    {
        $this->formattedData['highlighted'] = $this->menuItem->isHighlighted();

        return $this;
    }

    /**
     * Formats the children.
     *
     * @return MenuItemFormatterInterface
     */
    public function children(): MenuItemFormatterInterface
    {
        $menuItemFormatter = new MenuItemFormatter();
        $menuItemFormatter->setMenuFormatter($this->menuFormatter);
        $menuItemFormatter->setRouteFormatter($this->routeFormatter);
        $this->formattedData['children'] = array_map(function (MenuItem $menuItem) use ($menuItemFormatter) {
            return $menuItemFormatter
                ->init($menuItem)
                ->id()
                ->position()
                ->title()
                ->route()
                ->highlighted()
                ->pageType()
                ->children()
                ->format();
        }, $this->menuItem->getChildren()->toArray());

        return $this;
    }

    /**
     * @param MenuFormatterInterface $menuFormatter
     */
    public function setMenuFormatter(MenuFormatterInterface $menuFormatter): void
    {
        $this->menuFormatter = $menuFormatter;
    }

    /**
     * @param RouteFormatterInterface $routeFormatter
     */
    public function setRouteFormatter(RouteFormatterInterface $routeFormatter): void
    {
        $this->routeFormatter = $routeFormatter;
    }

    /**
     * Initializes the formatter.
     *
     * @param MenuItem $menuItem
     *
     * @return MenuItemFormatterInterface
     */
    public function init(MenuItem $menuItem): MenuItemFormatterInterface
    {
        $this->menuItem = $menuItem;

        return $this;
    }

    /**
     * Formats the parent.
     *
     * @return MenuItemFormatterInterface
     */
    public function parent(): MenuItemFormatterInterface
    {
        if ($this->menuItem->getParent()) {
            $menuItemFormatter = new MenuItemFormatter();
            $menuItemFormatter->setMenuFormatter($this->menuFormatter);
            $menuItemFormatter->setRouteFormatter($this->routeFormatter);

            $this->formattedData['parent'] = $menuItemFormatter->init($this->menuItem->getParent())->title()->format();
        } else {
            $this->formattedData['menu'] = $this->menuFormatter->init($this->menuItem->getMenu())->name()->format();
        }

        return $this;
    }

    /**
     * Formats the route.
     *
     * @return MenuItemFormatterInterface
     */
    public function route(): MenuItemFormatterInterface
    {
        $this->formattedData['route'] = $this->routeFormatter
            ->init($this->menuItem->getRoute())
            ->url()
            ->name()
            ->parameter()
            ->format();

        return $this;
    }

    /**
     * Formats the position.
     *
     * @return MenuItemFormatterInterface
     */
    public function position(): MenuItemFormatterInterface
    {
        $this->formattedData['position'] = $this->menuItem->getPosition();

        return $this;
    }
}
