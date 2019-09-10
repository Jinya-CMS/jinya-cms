<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\HistoryEnabledEntity;
use Jinya\Entity\Base\SlugEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery")
 */
class Gallery extends HistoryEnabledEntity
{
    use SlugEntity;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Media\GalleryFilePosition", mappedBy="gallery")
     */
    private $files;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $description;

    /**
     * Gallery constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles(): ArrayCollection
    {
        return $this->files;
    }

    /**
     * @param ArrayCollection $files
     */
    public function setFiles(ArrayCollection $files): void
    {
        $this->files = $files;
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
    public function setName(string $name): void
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
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'description' => $this->description,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
