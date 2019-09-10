<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;
use Jinya\Entity\Base\HistoryEnabledEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="folder")
 */
class Folder extends HistoryEnabledEntity
{
    use BaseEntity;

    /**
     * @var Folder
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Folder", inversedBy="childFolders")
     */
    private $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Media\Folder", mappedBy="parent")
     */
    private $childFolders;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $tags;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Media\File", mappedBy="folder")
     */
    private $files;

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
            'parent' => $this->parent->getId(),
            'name' => $this->name,
            'tags' => $this->tags,
        ];
    }
}
