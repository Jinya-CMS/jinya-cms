<?php

namespace Jinya\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery_file_position")
 * @UniqueEntity(fields={"gallery", "position"})
 */
class GalleryFilePosition
{
    use BaseEntity;

    /**
     * @ORM\Column(type="integer")
     */
    private int $position = -1;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Gallery", inversedBy="files")
     */
    private Gallery $gallery;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\File", inversedBy="galleries", cascade={"persist"})
     */
    private File $file;

    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    public function setGallery(Gallery $gallery): void
    {
        $this->gallery = $gallery;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
