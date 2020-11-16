<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 08:19
 */

namespace Jinya\Formatter\Form;

use Jinya\Entity\Form\FormItem;
use Jinya\Formatter\User\UserFormatterInterface;

class FormItemFormatter implements FormItemFormatterInterface
{
    private array $formattedData;

    private FormFormatterInterface $formFormatter;

    private UserFormatterInterface $userFormatter;

    private FormItem $formItem;

    public function setUserFormatter(UserFormatterInterface $userFormatter): void
    {
        $this->userFormatter = $userFormatter;
    }

    public function setFormFormatter(FormFormatterInterface $formFormatter): void
    {
        $this->formFormatter = $formFormatter;
    }

    /**
     * Formats the id
     */
    public function id(): FormItemFormatterInterface
    {
        $this->formattedData['id'] = $this->formItem->getId();

        return $this;
    }

    /**
     * Formats the type
     */
    public function type(): FormItemFormatterInterface
    {
        $this->formattedData['type'] = $this->formItem->getType();

        return $this;
    }

    /**
     * Formats the options
     */
    public function options(): FormItemFormatterInterface
    {
        $this->formattedData['options'] = $this->formItem->getOptions();

        return $this;
    }

    /**
     * Formats the label
     */
    public function label(): FormItemFormatterInterface
    {
        $this->formattedData['label'] = $this->formItem->getLabel();

        return $this;
    }

    /**
     * Formats the help text
     */
    public function helpText(): FormItemFormatterInterface
    {
        $this->formattedData['helptext'] = $this->formItem->getHelpText();

        return $this;
    }

    /**
     * Formats the form
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
     */
    public function init(FormItem $formItem): FormItemFormatterInterface
    {
        $this->formItem = $formItem;

        return $this;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Formats the position
     */
    public function position(): FormItemFormatterInterface
    {
        $this->formattedData['position'] = $this->formItem->getPosition();

        return $this;
    }

    /**
     * Formats the created info
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
     */
    public function history(): FormItemFormatterInterface
    {
        $this->formattedData['history'] = $this->formItem->getHistory();

        return $this;
    }

    /**
     * Formats the spam filter
     */
    public function spamFilter(): FormItemFormatterInterface
    {
        $this->formattedData['spamFilter'] = $this->formItem->getSpamFilter();

        return $this;
    }
}
