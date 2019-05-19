<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 08:11
 */

namespace Jinya\Formatter\Form;

use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\FormItem;
use Jinya\Formatter\User\UserFormatterInterface;

class FormFormatter implements FormFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var Form */
    private $form;

    /** @var FormItemFormatterInterface */
    private $formItemFormatter;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /**
     * @param UserFormatterInterface $userFormatter
     */
    public function setUserFormatter(UserFormatterInterface $userFormatter): void
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * @param FormItemFormatterInterface $formItemFormatter
     */
    public function setFormItemFormatter(FormItemFormatterInterface $formItemFormatter): void
    {
        $this->formItemFormatter = $formItemFormatter;
    }

    /**
     * Initializes the formatter
     *
     * @param Form $form
     * @return FormFormatterInterface
     */
    public function init(Form $form): FormFormatterInterface
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Formats the to address
     *
     * @return FormFormatterInterface
     */
    public function toAddress(): FormFormatterInterface
    {
        $this->formattedData['toAddress'] = $this->form->getToAddress();

        return $this;
    }

    /**
     * Formats the title
     *
     * @return FormFormatterInterface
     */
    public function title(): FormFormatterInterface
    {
        $this->formattedData['title'] = $this->form->getTitle();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return FormFormatterInterface
     */
    public function description(): FormFormatterInterface
    {
        $this->formattedData['description'] = $this->form->getDescription();

        return $this;
    }

    /**
     * Formats the email template
     *
     * @return FormFormatterInterface
     */
    public function emailTemplate(): FormFormatterInterface
    {
        $this->formattedData['emailTemplate'] = $this->form->getEmailTemplate();

        return $this;
    }

    /**
     * Formats the items
     *
     * @return FormFormatterInterface
     */
    public function items(): FormFormatterInterface
    {
        $data = [];

        /** @var FormItem $item */
        foreach ($this->form->getItems() as $item) {
            $data[] = $this->formItemFormatter
                ->init($item)
                ->id()
                ->label()
                ->type()
                ->options()
                ->helpText()
                ->format();
        }

        $this->formattedData['items'] = $data;

        return $this;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     *
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Formats the slug
     *
     * @return FormFormatterInterface
     */
    public function slug(): FormFormatterInterface
    {
        $this->formattedData['slug'] = $this->form->getSlug();

        return $this;
    }

    /**
     * Formats the id
     *
     * @return FormFormatterInterface
     */
    public function id(): FormFormatterInterface
    {
        $this->formattedData['id'] = $this->form->getId();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return FormFormatterInterface
     */
    public function created(): FormFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->form->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->form->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return FormFormatterInterface
     */
    public function updated(): FormFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->form->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->form->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return FormFormatterInterface
     */
    public function history(): FormFormatterInterface
    {
        $this->formattedData['history'] = $this->form->getHistory();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return FormFormatterInterface
     */
    public function name(): FormFormatterInterface
    {
        $this->formattedData['name'] = $this->form->getName();

        return $this;
    }
}
