<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:40
 */

namespace DataBundle\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\OneToMany(mappedBy="form", targetEntity="DataBundle\Entity\FormItem", cascade={"persist", "remove"})
     */
    private $items;

    /**
     * @var string
     * @Assert\Email
     * @ORM\Column(type="string")
     */
    private $toAddress;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;
    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $emailTemplate = '';

    /**
     * Form constructor.
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getEmailTemplate(): string
    {
        return $this->emailTemplate;
    }

    /**
     * @param string $emailTemplate
     */
    public function setEmailTemplate(string $emailTemplate): void
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @return Collection
     */
    public function getItems(): ?Collection
    {
        return $this->items;
    }

    /**
     * @param Collection $items
     */
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

    /**
     * @param string $toAddress
     */
    public function setToAddress(string $toAddress): void
    {
        $this->toAddress = $toAddress;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
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
            'description' => $this->description
        ];
    }
}