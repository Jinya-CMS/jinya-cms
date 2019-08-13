<?php

namespace Jinya\Formatter\Form;

use Jinya\Entity\Form\Message;
use Jinya\Formatter\FormatterInterface;

interface MessageFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param Message $message
     * @return MessageFormatterInterface
     */
    public function init(Message $message): self;

    /**
     * Formats the to address
     *
     * @return MessageFormatterInterface
     */
    public function toAddress(): self;

    /**
     * Formats the form
     *
     * @return MessageFormatterInterface
     */
    public function form(): self;

    /**
     * Formats the subject
     *
     * @return MessageFormatterInterface
     */
    public function subject(): self;

    /**
     * Formats the content
     *
     * @return MessageFormatterInterface
     */
    public function content(): self;

    /**
     * Formats the fromAddress
     *
     * @return MessageFormatterInterface
     */
    public function fromAddress(): self;

    /**
     * Formats whether the message is spam
     *
     * @return MessageFormatterInterface
     */
    public function spam(): self;

    /**
     * Formats the send at date
     *
     * @return MessageFormatterInterface
     */
    public function sendAt(): self;
}
