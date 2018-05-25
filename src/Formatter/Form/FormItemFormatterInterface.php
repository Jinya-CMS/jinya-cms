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
    public function id(): FormItemFormatterInterface;

    /**
     * Formats the type.
     *
     * @return FormItemFormatterInterface
     */
    public function type(): FormItemFormatterInterface;

    /**
     * Formats the options.
     *
     * @return FormItemFormatterInterface
     */
    public function options(): FormItemFormatterInterface;

    /**
     * Formats the label.
     *
     * @return FormItemFormatterInterface
     */
    public function label(): FormItemFormatterInterface;

    /**
     * Formats the help text.
     *
     * @return FormItemFormatterInterface
     */
    public function helpText(): FormItemFormatterInterface;

    /**
     * Formats the form.
     *
     * @return FormItemFormatterInterface
     */
    public function form(): FormItemFormatterInterface;

    /**
     * Formats the position.
     *
     * @return FormItemFormatterInterface
     */
    public function position(): FormItemFormatterInterface;

    /**
     * Formats the created info.
     *
     * @return FormItemFormatterInterface
     */
    public function created(): FormItemFormatterInterface;

    /**
     * Formats the updated info.
     *
     * @return FormItemFormatterInterface
     */
    public function updated(): FormItemFormatterInterface;

    /**
     * Formats the history.
     *
     * @return FormItemFormatterInterface
     */
    public function history(): FormItemFormatterInterface;

    /**
     * Initializes the formatter.
     *
     * @param FormItem $formItem
     *
     * @return FormItemFormatterInterface
     */
    public function init(FormItem $formItem): FormItemFormatterInterface;
}
