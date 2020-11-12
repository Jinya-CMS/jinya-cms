<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 08:09
 */

namespace Jinya\Formatter\Form;

use Jinya\Entity\Form\Form;
use Jinya\Formatter\FormatterInterface;

interface FormFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @return FormFormatterInterface
     */
    public function init(Form $form): self;

    /**
     * Formats the to address
     *
     * @return FormFormatterInterface
     */
    public function toAddress(): self;

    /**
     * Formats the title
     *
     * @return FormFormatterInterface
     */
    public function title(): self;

    /**
     * Formats the description
     *
     * @return FormFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the email template
     *
     * @return FormFormatterInterface
     */
    public function emailTemplate(): self;

    /**
     * Formats the items
     *
     * @return FormFormatterInterface
     */
    public function items(): self;

    /**
     * Formats the slug
     *
     * @return FormFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the id
     *
     * @return FormFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the created info
     *
     * @return FormFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return FormFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return FormFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the name
     *
     * @return FormFormatterInterface
     */
    public function name(): self;
}
