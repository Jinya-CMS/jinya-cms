<?php

namespace Jinya\Formatter\Form;

use Jinya\Entity\Form\Message;

class MessageFormatter implements MessageFormatterInterface
{
    /** @var array */
    private array $formatted;

    /** @var Message */
    private Message $message;

    /** @var FormFormatterInterface */
    private FormFormatterInterface $formFormatter;

    /**
     * MessageFormatter constructor.
     * @param FormFormatterInterface $formFormatter
     */
    public function __construct(FormFormatterInterface $formFormatter)
    {
        $this->formFormatter = $formFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formatted;
    }

    /**
     * Initializes the formatting
     *
     * @param Message $message
     * @return MessageFormatterInterface
     */
    public function init(Message $message): MessageFormatterInterface
    {
        $this->formatted = [];
        $this->message = $message;

        return $this;
    }

    /**
     * Formats the to address
     *
     * @return MessageFormatterInterface
     */
    public function toAddress(): MessageFormatterInterface
    {
        $this->formatted['toAddress'] = $this->message->getTargetAddress();

        return $this;
    }

    /**
     * Formats the form
     *
     * @return MessageFormatterInterface
     */
    public function form(): MessageFormatterInterface
    {
        $this->formatted['form'] = $this->formFormatter
            ->init($this->message->getForm())
            ->description()
            ->title()
            ->name()
            ->slug();

        return $this;
    }

    /**
     * Formats whether the message is spam
     *
     * @return MessageFormatterInterface
     */
    public function spam(): MessageFormatterInterface
    {
        $this->formatted['spam'] = $this->message->isSpam();

        return $this;
    }

    /**
     * Formats the send at date
     *
     * @return MessageFormatterInterface
     */
    public function sendAt(): MessageFormatterInterface
    {
        $this->formatted['sendAt'] = $this->message->getSendAt()->format(DATE_ATOM);

        return $this;
    }

    /**
     * Formats the subject
     *
     * @return MessageFormatterInterface
     */
    public function subject(): MessageFormatterInterface
    {
        $this->formatted['subject'] = $this->message->getSubject();

        return $this;
    }

    /**
     * Formats the content
     *
     * @return MessageFormatterInterface
     */
    public function content(): MessageFormatterInterface
    {
        $this->formatted['content'] = $this->message->getContent();

        return $this;
    }

    /**
     * Formats the fromAddress
     *
     * @return MessageFormatterInterface
     */
    public function fromAddress(): MessageFormatterInterface
    {
        $this->formatted['fromAddress'] = $this->message->getFromAddress();

        return $this;
    }

    /**
     * Formats whether the message is archived
     *
     * @return MessageFormatterInterface
     */
    public function archived(): MessageFormatterInterface
    {
        $this->formatted['archived'] = $this->message->isArchived();

        return $this;
    }

    /**
     * Formats whether the message is in trash
     *
     * @return MessageFormatterInterface
     */
    public function trash(): MessageFormatterInterface
    {
        $this->formatted['trash'] = $this->message->isDeleted();

        return $this;
    }

    /**
     * Formats whether the message is read
     *
     * @return MessageFormatterInterface
     */
    public function read(): MessageFormatterInterface
    {
        $this->formatted['read'] = $this->message->isRead();

        return $this;
    }

    /**
     * Formats the id
     *
     * @return MessageFormatterInterface
     */
    public function id(): MessageFormatterInterface
    {
        $this->formatted['id'] = $this->message->getId();

        return $this;
    }
}
