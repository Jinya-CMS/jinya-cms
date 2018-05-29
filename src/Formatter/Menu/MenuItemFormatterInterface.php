<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:10
 */

namespace Jinya\Formatter\Menu;

use Jinya\Entity\MenuItem;
use Jinya\Formatter\FormatterInterface;

interface MenuItemFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @param MenuItem $menuItem
     * @return MenuItemFormatterInterface
     */
    public function init(MenuItem $menuItem): self;

    /**
     * Formats the id
     *
     * @return MenuItemFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the title
     *
     * @return MenuItemFormatterInterface
     */
    public function title(): self;

    /**
     * Formats the page type
     *
     * @return MenuItemFormatterInterface
     */
    public function pageType(): self;

    /**
     * Formats the highlight state
     *
     * @return MenuItemFormatterInterface
     */
    public function highlighted(): self;

    /**
     * Formats the children
     *
     * @return MenuItemFormatterInterface
     */
    public function children(): self;

    /**
     * Formats the parent
     *
     * @return MenuItemFormatterInterface
     */
    public function parent(): self;

    /**
     * Formats the route
     *
     * @return MenuItemFormatterInterface
     */
    public function route(): self;

    /**
     * Formats the position
     *
     * @return MenuItemFormatterInterface
     */
    public function position(): self;
}
