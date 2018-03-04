<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:29
 */

namespace Jinya\Formatter\Route;


use Jinya\Entity\RoutingEntry;
use Jinya\Formatter\FormatterInterface;

interface RouteFormatterInterface extends FormatterInterface
{
    /**
     * Formats the route name
     *
     * @return RouteFormatterInterface
     */
    public function name(): RouteFormatterInterface;

    /**
     * Formats the route parameter
     *
     * @return RouteFormatterInterface
     */
    public function parameter(): RouteFormatterInterface;

    /**
     * Formats the url
     *
     * @return RouteFormatterInterface
     */
    public function url(): RouteFormatterInterface;

    /**
     * Formats the menu item
     *
     * @return RouteFormatterInterface
     */
    public function menuItem(): RouteFormatterInterface;

    /**
     * Initializes the formatter
     *
     * @param RoutingEntry $routingEntry
     * @return RouteFormatterInterface
     */
    public function init(RoutingEntry $routingEntry): RouteFormatterInterface;
}