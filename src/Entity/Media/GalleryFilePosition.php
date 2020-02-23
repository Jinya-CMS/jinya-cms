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
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $position;

    /**
     * @var Gallery
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Gallery", inversedBy="files")
     */
    private Gallery $gallery;

    /**
     * @var File
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\File", inversedBy="galleries", cascade={"persist"})
     */
    private File $file;

    /**
     * @return Gallery
     */
    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery(Gallery $gallery): void
    {
        $this->gallery = $gallery;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
