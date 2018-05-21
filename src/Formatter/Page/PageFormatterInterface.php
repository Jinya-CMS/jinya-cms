<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 22:50
 */

namespace Jinya\Formatter\Page;


use Jinya\Entity\Page;
use Jinya\Formatter\FormatterInterface;

interface PageFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @param Page $page
     * @return PageFormatterInterface
     */
    public function init(Page $page): PageFormatterInterface;

    /**
     * Formats the id
     *
     * @return PageFormatterInterface
     */
    public function id(): PageFormatterInterface;

    /**
     * Formats the created info
     *
     * @return PageFormatterInterface
     */
    public function created(): PageFormatterInterface;

    /**
     * Formats the updated info
     *
     * @return PageFormatterInterface
     */
    public function updated(): PageFormatterInterface;

    /**
     * Formats the history
     *
     * @return PageFormatterInterface
     */
    public function history(): PageFormatterInterface;

    /**
     * Formats the content
     *
     * @return PageFormatterInterface
     */
    public function content(): PageFormatterInterface;

    /**
     * Formats the title
     *
     * @return PageFormatterInterface
     */
    public function title(): PageFormatterInterface;

    /**
     * Formats the slug
     *
     * @return PageFormatterInterface
     */
    public function slug(): PageFormatterInterface;

    /**
     * Formats the name
     *
     * @return PageFormatterInterface
     */
    public function name(): PageFormatterInterface;
}