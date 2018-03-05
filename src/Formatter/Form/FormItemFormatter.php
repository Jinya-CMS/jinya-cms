<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 08:19
 */

namespace Jinya\Formatter\Form;


use Jinya\Entity\FormItem;
use Jinya\Formatter\User\UserFormatterInterface;

class FormItemFormatter implements FormItemFormatterInterface
{
    /** @var array */
    private $formattedData;
    /** @var FormFormatterInterface */
    private $formFormatter;
    /** @var UserFormatterInterface */
    private $userFormatter;
    /** @var FormItem */
    private $formItem;

    /**
     * @param UserFormatterInterface $userFormatter
     */
    public function setUserFormatter(UserFormatterInterface $userFormatter): void
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * @param FormFormatterInterface $formFormatter
     */
    public function setFormFormatter(FormFormatterInterface $formFormatter): void
    {
        $this->formFormatter = $formFormatter;
    }

    /**
     * Formats the id
     *
     * @return FormItemFormatterInterface
     */
    public function id(): FormItemFormatterInterface
    {
        $this->formattedData['id'] = $this->formItem->getId();

        return $this;
    }

    /**
     * Formats the type
     *
     * @return FormItemFormatterInterface
     */
    public function type(): FormItemFormatterInterface
    {
        $this->formattedData['type'] = $this->formItem->getType();

        return $this;
    }

    /**
     * Formats the options
     *
     * @return FormItemFormatterInterface
     */
    public function options(): FormItemFormatterInterface
    {
        $this->formattedData['options'] = $this->formItem->getOptions();

        return $this;
    }

    /**
     * Formats the label
     *
     * @return FormItemFormatterInterface
     */
    public function label(): FormItemFormatterInterface
    {
        $this->formattedData['label'] = $this->formItem->getLabel();

        return $this;
    }

    /**
     * Formats the help text
     *
     * @return FormItemFormatterInterface
     */
    public function helpText(): FormItemFormatterInterface
    {
        $this->formattedData['helptext'] = $this->formItem->getHelpText();

        return $this;
    }

    /**
     * Formats the form
     *
     * @return FormItemFormatterInterface
     */
    public function form(): FormItemFormatterInterface
    {

        $this->formattedData['form'] = $this->formFormatter
            ->init($this->formItem->getForm())
            ->title()
            ->slug()
            ->format();

        return $this;
    }

    /**
     * Initializes the formatter
     *
     * @param FormItem $formItem
     * @return FormItemFormatterInterface
     */
    public function init(FormItem $formItem): FormItemFormatterInterface
    {
        $this->formItem = $formItem;

        return $this;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Formats the position
     *
     * @return FormItemFormatterInterface
     */
    public function position(): FormItemFormatterInterface
    {
        $this->formattedData['position'] = $this->formItem->getPosition();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return FormItemFormatterInterface
     */
    public function created(): FormItemFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->formItem->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->formItem->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return FormItemFormatterInterface
     */
    public function updated(): FormItemFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->formItem->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->formItem->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return FormItemFormatterInterface
     */
    public function history(): FormItemFormatterInterface
    {
        $this->formattedData['history'] = $this->formItem->getHistory();

        return $this;
    }
}