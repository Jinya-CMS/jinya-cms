<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:40
 */

namespace Jinya\Entity\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\HistoryEnabledEntity;
use Jinya\Entity\Base\SlugEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="form")
 */
class Form extends HistoryEnabledEntity
{
    use SlugEntity;

    /**
     * @var Collection
     * @ORM\OneToMany(mappedBy="form", targetEntity="Jinya\Entity\Form\FormItem", cascade={"persist", "remove"})
     */
    private $items;

    /**
     * @var Collection
     * @ORM\OneToMany(mappedBy="form", targetEntity="Jinya\Entity\Form\Message", cascade={"persist", "remove"})
     */
    private $messages;

    /**
     * @Assert\Email
     * @ORM\Column(type="string")
     */
    private string $toAddress;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\Column(type="text")
     */
    private string $emailTemplate = '';

    /**
     * Form constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getEmailTemplate(): string
    {
        return $this->emailTemplate;
    }

    public function setEmailTemplate(string $emailTemplate): void
    {
        $this->emailTemplate = $emailTemplate;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getToAddress(): ?string
    {
        return $this->toAddress;
    }

    public function setToAddress(string $toAddress): void
    {
        $this->toAddress = $toAddress;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function setMessages(Collection $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'items' => $this->items->toArray(),
            'toAddress' => $this->toAddress,
            'emailTemplate' => $this->emailTemplate,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
        ];
    }
}
