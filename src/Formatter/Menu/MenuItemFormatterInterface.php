<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:10.
 */

namespace Jinya\Formatter\Menu;

use Jinya\Entity\MenuItem;
use Jinya\Formatter\FormatterInterface;

interface MenuItemFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter.
     *
     * @param MenuItem $menuItem
     *
     * @return MenuItemFormatterInterface
     */
    public function init(MenuItem $menuItem): MenuItemFormatterInterface;

    /**
     * Formats the id.
     *
     * @return MenuItemFormatterInterface
     */
    public function id(): MenuItemFormatterInterface;

    /**
     * Formats the title.
     *
     * @return MenuItemFormatterInterface
     */
    public function title(): MenuItemFormatterInterface;

    /**
     * Formats the page type.
     *
     * @return MenuItemFormatterInterface
     */
    public function pageType(): MenuItemFormatterInterface;

    /**
     * Formats the highlight state.
     *
     * @return MenuItemFormatterInterface
     */
    public function highlighted(): MenuItemFormatterInterface;

    /**
     * Formats the children.
     *
     * @return MenuItemFormatterInterface
     */
    public function children(): MenuItemFormatterInterface;

    /**
     * Formats the parent.
     *
     * @return MenuItemFormatterInterface
     */
    public function parent(): MenuItemFormatterInterface;

    /**
     * Formats the route.
     *
     * @return MenuItemFormatterInterface
     */
    public function route(): MenuItemFormatterInterface;

    /**
     * Formats the position.
     *
     * @return MenuItemFormatterInterface
     */
    public function position(): MenuItemFormatterInterface;
}
