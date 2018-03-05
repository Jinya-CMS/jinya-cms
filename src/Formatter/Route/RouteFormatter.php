<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:34
 */

namespace Jinya\Formatter\Route;


use Jinya\Entity\RoutingEntry;
use Jinya\Formatter\Menu\MenuItemFormatterInterface;

class RouteFormatter implements RouteFormatterInterface
{
    /** @var MenuItemFormatterInterface */
    private $menuItemFormatter;

    /** @var array */
    private $formattedData;
    /** @var RoutingEntry */
    private $route;

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
     * Formats the route name
     *
     * @return RouteFormatterInterface
     */
    public function name(): RouteFormatterInterface
    {
        $this->formattedData['name'] = $this->route->getRouteName();

        return $this;
    }

    /**
     * Formats the route parameter
     *
     * @return RouteFormatterInterface
     */
    public function parameter(): RouteFormatterInterface
    {
        $this->formattedData['parameter'] = $this->route->getRouteParameter();

        return $this;
    }

    /**
     * Formats the url
     *
     * @return RouteFormatterInterface
     */
    public function url(): RouteFormatterInterface
    {
        $this->formattedData['url'] = $this->route->getUrl();

        return $this;
    }

    /**
     * Formats the menu item
     *
     * @return RouteFormatterInterface
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
     *
     * @param RoutingEntry $routingEntry
     * @return RouteFormatterInterface
     */
    public function init(RoutingEntry $routingEntry): RouteFormatterInterface
    {
        $this->route = $routingEntry;

        return $this;
    }
}