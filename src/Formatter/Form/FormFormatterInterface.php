<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 08:09
 */

namespace Jinya\Formatter\Form;


use Jinya\Entity\Form;
use Jinya\Formatter\FormatterInterface;

interface FormFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatter
     *
     * @param Form $form
     * @return FormFormatterInterface
     */
    public function init(Form $form): FormFormatterInterface;

    /**
     * Formats the to address
     *
     * @return FormFormatterInterface
     */
    public function toAddress(): FormFormatterInterface;

    /**
     * Formats the title
     *
     * @return FormFormatterInterface
     */
    public function title(): FormFormatterInterface;

    /**
     * Formats the description
     *
     * @return FormFormatterInterface
     */
    public function description(): FormFormatterInterface;

    /**
     * Formats the email template
     *
     * @return FormFormatterInterface
     */
    public function emailTemplate(): FormFormatterInterface;

    /**
     * Formats the items
     *
     * @return FormFormatterInterface
     */
    public function items(): FormFormatterInterface;

    /**
     * Formats the slug
     *
     * @return FormFormatterInterface
     */
    public function slug(): FormFormatterInterface;

    /**
     * Formats the id
     *
     * @return FormFormatterInterface
     */
    public function id(): FormFormatterInterface;


    /**
     * Formats the created info
     *
     * @return FormFormatterInterface
     */
    public function created(): FormFormatterInterface;

    /**
     * Formats the updated info
     *
     * @return FormFormatterInterface
     */
    public function updated(): FormFormatterInterface;

    /**
     * Formats the history
     *
     * @return FormFormatterInterface
     */
    public function history(): FormFormatterInterface;
}