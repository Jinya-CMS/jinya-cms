<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:29
 */

namespace Jinya\Formatter\Route;

use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Formatter\FormatterInterface;

interface RouteFormatterInterface extends FormatterInterface
{
    /**
     * Formats the route name
     *
     * @return RouteFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the route parameter
     *
     * @return RouteFormatterInterface
     */
    public function parameter(): self;

    /**
     * Formats the url
     *
     * @return RouteFormatterInterface
     */
    public function url(): self;

    /**
     * Formats the menu item
     *
     * @return RouteFormatterInterface
     */
    public function menuItem(): self;

    /**
     * Initializes the formatter
     *
     * @param \Jinya\Entity\Menu\RoutingEntry $routingEntry
     * @return RouteFormatterInterface
     */
    public function init(RoutingEntry $routingEntry): self;
}
