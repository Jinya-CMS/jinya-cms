<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;
use Jinya\Entity\Base\HistoryEnabledEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="file", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="folder_and_name", columns={"folder_id", "name"})
 * })
 */
class File extends HistoryEnabledEntity
{
    use BaseEntity;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $path = '';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $type = '';

    /**
     * @var Folder
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Folder", inversedBy="files")
     */
    private $folder;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Media\GalleryFilePosition", mappedBy="file")
     */
    private $galleries;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Media\Tag", mappedBy="files")
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

    /**
     * @return ArrayCollection
     */
    public function getGalleries(): ArrayCollection
    {
        return $this->galleries;
    }

    /**
     * @param ArrayCollection $galleries
     */
    public function setGalleries(ArrayCollection $galleries): void
    {
        $this->galleries = $galleries;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags(): ArrayCollection
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags(ArrayCollection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Folder
     */
    public function getFolder(): Folder
    {
        return $this->folder;
    }

    /**
     * @param Folder $folder
     */
    public function setFolder(Folder $folder): void
    {
        $this->folder = $folder;
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
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'folder' => $this->folder->getId(),
            'path' => $this->path,
            'name' => $this->name,
            'type' => $this->type,
            'tags' => $this->tags,
        ];
    }
}
