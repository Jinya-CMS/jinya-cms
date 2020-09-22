<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:24
 */

namespace Jinya\Formatter\Menu;

use Jinya\Entity\Menu\MenuItem;
use Jinya\Formatter\Route\RouteFormatterInterface;

class MenuItemFormatter implements MenuItemFormatterInterface
{
    /** @var array */
    private array $formattedData;

    /** @var MenuItem */
    private MenuItem $menuItem;

    /** @var MenuFormatterInterface */
    private MenuFormatterInterface $menuFormatter;

    /** @var RouteFormatterInterface */
    private RouteFormatterInterface $routeFormatter;

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Formats the id
     */
    public function id(): MenuItemFormatterInterface
    {
        $this->formattedData['id'] = $this->menuItem->getId();

        return $this;
    }

    /**
     * Formats the title
     */
    public function title(): MenuItemFormatterInterface
    {
        $this->formattedData['title'] = $this->menuItem->getTitle();

        return $this;
    }

    /**
     * Formats the page type
     */
    public function pageType(): MenuItemFormatterInterface
    {
        $this->formattedData['pageType'] = $this->menuItem->getPageType();

        return $this;
    }

    /**
     * Formats the highlight state
     */
    public function highlighted(): MenuItemFormatterInterface
    {
        $this->formattedData['highlighted'] = $this->menuItem->isHighlighted();

        return $this;
    }

    /**
     * Formats the children
     */
    public function children(): MenuItemFormatterInterface
    {
        $menuItemFormatter = new self();
        $menuItemFormatter->setMenuFormatter($this->menuFormatter);
        $menuItemFormatter->setRouteFormatter($this->routeFormatter);
        $this->formattedData['children'] = array_map(static function (MenuItem $menuItem) use ($menuItemFormatter) {
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

    public function setMenuFormatter(MenuFormatterInterface $menuFormatter): void
    {
        $this->menuFormatter = $menuFormatter;
    }

    public function setRouteFormatter(RouteFormatterInterface $routeFormatter): void
    {
        $this->routeFormatter = $routeFormatter;
    }

    /**
     * Initializes the formatter
     */
    public function init(MenuItem $menuItem): MenuItemFormatterInterface
    {
        $this->menuItem = $menuItem;

        return $this;
    }

    /**
     * Formats the parent
     */
    public function parent(): MenuItemFormatterInterface
    {
        if ($this->menuItem->getParent()) {
            $menuItemFormatter = new self();
            $menuItemFormatter->setMenuFormatter($this->menuFormatter);
            $menuItemFormatter->setRouteFormatter($this->routeFormatter);

            $this->formattedData['parent'] = $menuItemFormatter->init($this->menuItem->getParent())->title()->format();
        } else {
            $this->formattedData['menu'] = $this->menuFormatter->init($this->menuItem->getMenu())->name()->format();
        }

        return $this;
    }

    /**
     * Formats the route
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
     * Formats the position
     */
    public function position(): MenuItemFormatterInterface
    {
        $this->formattedData['position'] = $this->menuItem->getPosition();

        return $this;
    }
}
