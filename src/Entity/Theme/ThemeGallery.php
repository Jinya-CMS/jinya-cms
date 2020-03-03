<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Media\Gallery;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_gallery")
 */
class ThemeGallery
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(nullable=false, name="theme_id", referencedColumnName="id")
     * @var Theme
     */
    private Theme $theme;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Gallery")
     * @ORM\JoinColumn(nullable=false, name="gallery_id", referencedColumnName="id")
     * @var Gallery
     */
    private Gallery $gallery;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private string $name;

    /**
     * @return Theme
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

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
}
