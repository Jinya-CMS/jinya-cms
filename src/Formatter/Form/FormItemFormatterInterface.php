<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 08:15.
 */

namespace Jinya\Formatter\Form;

use Jinya\Entity\FormItem;
use Jinya\Formatter\FormatterInterface;

interface FormItemFormatterInterface extends FormatterInterface
{
    /**
     * Formats the id.
     *
     * @return FormItemFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the type.
     *
     * @return FormItemFormatterInterface
     */
    public function type(): self;

    /**
     * Formats the options.
     *
     * @return FormItemFormatterInterface
     */
    public function options(): self;

    /**
     * Formats the label.
     *
     * @return FormItemFormatterInterface
     */
    public function label(): self;

    /**
     * Formats the help text.
     *
     * @return FormItemFormatterInterface
     */
    public function helpText(): self;

    /**
     * Formats the form.
     *
     * @return FormItemFormatterInterface
     */
    public function form(): self;

    /**
     * Formats the position.
     *
     * @return FormItemFormatterInterface
     */
    public function position(): self;

    /**
     * Formats the created info.
     *
     * @return FormItemFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info.
     *
     * @return FormItemFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history.
     *
     * @return FormItemFormatterInterface
     */
    public function history(): self;

    /**
     * Initializes the formatter.
     *
     * @param FormItem $formItem
     *
     * @return FormItemFormatterInterface
     */
    public function init(FormItem $formItem): self;
}
