<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:34
 */

namespace Jinya\Formatter\Route;

use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Formatter\Menu\MenuItemFormatterInterface;

class RouteFormatter implements RouteFormatterInterface
{
    private MenuItemFormatterInterface $menuItemFormatter;

    private array $formattedData;

    private RoutingEntry $route;

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
     * Formats the route name
     */
    public function name(): RouteFormatterInterface
    {
        $this->formattedData['name'] = $this->route->getRouteName();

        return $this;
    }

    /**
     * Formats the route parameter
     */
    public function parameter(): RouteFormatterInterface
    {
        $this->formattedData['parameter'] = $this->route->getRouteParameter();

        return $this;
    }

    /**
     * Formats the url
     */
    public function url(): RouteFormatterInterface
    {
        $this->formattedData['url'] = $this->route->getUrl();

        return $this;
    }

    /**
     * Formats the menu item
     */
    public function menuItem(): RouteFormatterInterface
    {
        $this->formattedData['menuItem'] = $this->menuItemFormatter->init($this->route->getMenuItem())
            ->title()
            ->id()
            ->format();

        return $this;
    }

    /**
     * Initializes the formatter
     */
    public function init(RoutingEntry $routingEntry): RouteFormatterInterface
    {
        $this->route = $routingEntry;

        return $this;
    }
}
