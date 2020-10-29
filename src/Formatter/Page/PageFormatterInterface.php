<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 22:50
 */

namespace Jinya\Formatter\Page;

use Jinya\Entity\Page\Page;
use Jinya\Formatter\FormatterInterface;

interface PageFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @return PageFormatterInterface
     */
    public function init(Page $page): self;

    /**
     * Formats the id
     *
     * @return PageFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the created info
     *
     * @return PageFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return PageFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return PageFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the content
     *
     * @return PageFormatterInterface
     */
    public function content(): self;

    /**
     * Formats the title
     *
     * @return PageFormatterInterface
     */
    public function title(): self;

    /**
     * Formats the slug
     *
     * @return PageFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the name
     *
     * @return PageFormatterInterface
     */
    public function name(): self;
}
