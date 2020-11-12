<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;
use Jinya\Entity\Base\HistoryEnabledEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="file", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="name", columns={"name"})
 * })
 */
class File extends HistoryEnabledEntity
{
    use BaseEntity;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $path = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $type = '';

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Media\GalleryFilePosition", mappedBy="file", cascade={"remove"})
     */
    private $galleries;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Media\Tag", inversedBy="files", cascade={"persist"})
     */
    private $tags;

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->galleries = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function setGalleries(Collection $galleries): void
    {
        $this->galleries = $galleries;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
            'path' => $this->path,
            'name' => $this->name,
            'type' => $this->type,
            'tags' => $this->tags,
        ];
    }
}
