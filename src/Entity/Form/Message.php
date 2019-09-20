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
     * @var Form
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Form\Form", inversedBy="messages")
     */
    private $form;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $subject;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $fromAddress;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $spam = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $targetAddress;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $sendAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isArchived = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isDeleted = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isRead = false;

    public function __construct()
    {
        $this->sendAt = new DateTime('now');
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    /**
     * @param bool $isArchived
     */
    public function setIsArchived(bool $isArchived): void
    {
        $this->isArchived = $isArchived;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @param Form $form
     */
    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getTargetAddress(): string
    {
        return $this->targetAddress;
    }

    /**
     * @param string $targetAddress
     */
    public function setTargetAddress(string $targetAddress): void
    {
        $this->targetAddress = $targetAddress;
    }

    /**
     * @return bool
     */
    public function isSpam(): bool
    {
        return $this->spam;
    }

    /**
     * @param bool $spam
     */
    public function setSpam(bool $spam): void
    {
        $this->spam = $spam;
    }

    /**
     * @return DateTime
     */
    public function getSendAt(): DateTime
    {
        return $this->sendAt;
    }

    /**
     * @param DateTime $sendAt
     */
    public function setSendAt(DateTime $sendAt): void
    {
        $this->sendAt = $sendAt;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getFromAddress(): string
    {
        return $this->fromAddress;
    }

    /**
     * @param string $fromAddress
     */
    public function setFromAddress(string $fromAddress): void
    {
        $this->fromAddress = $fromAddress;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     */
    public function setIsRead(bool $isRead): void
    {
        $this->isRead = $isRead;
    }
}
