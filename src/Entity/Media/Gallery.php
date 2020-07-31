<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\OneToMany(
     *     targetEntity="Jinya\Entity\Media\GalleryFilePosition",
     *     mappedBy="gallery",
     *     cascade={"remove", "persist"}
     * )
     */
    private Collection $files;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $orientation;

    /**
     * Gallery constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getOrientation(): string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): void
    {
        $this->orientation = $orientation;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
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
