<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 07.11.2017
 * Time: 17:33
 */

namespace DataBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery")
 * @ORM\HasLifecycleCallbacks
 */
class Gallery extends HistoryEnabledEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $background;
    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $slug;

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
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
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getBackground(): string
    {
        return $this->background;
    }

    /**
     * @param string $background
     */
    public function setBackground(string $background)
    {
        $this->background = $background;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'history' => $this->getHistory(),
            'background' => $this->background,
            'creator' => $this->getCreator(),
            'createdAt' => $this->getCreatedAt(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy()
        ];
    }
}