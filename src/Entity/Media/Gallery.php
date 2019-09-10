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
