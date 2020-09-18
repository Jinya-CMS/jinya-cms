<?php

namespace Jinya\Entity\Form;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message
{
    use BaseEntity;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Form\Form", inversedBy="messages")
     */
    private Form $form;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $subject;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private string $content;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $fromAddress;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $spam = false;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $targetAddress;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private DateTime $sendAt;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isArchived = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isDeleted = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isRead = false;

    public function __construct()
    {
        $this->sendAt = new DateTime('now');
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): void
    {
        $this->isArchived = $isArchived;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    public function getTargetAddress(): string
    {
        return $this->targetAddress;
    }

    public function setTargetAddress(string $targetAddress): void
    {
        $this->targetAddress = $targetAddress;
    }

    public function isSpam(): bool
    {
        return $this->spam;
    }

    public function setSpam(bool $spam): void
    {
        $this->spam = $spam;
    }

    public function getSendAt(): DateTime
    {
        return $this->sendAt;
    }

    public function setSendAt(DateTime $sendAt): void
    {
        $this->sendAt = $sendAt;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getFromAddress(): string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(string $fromAddress): void
    {
        $this->fromAddress = $fromAddress;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): void
    {
        $this->isRead = $isRead;
    }
}
