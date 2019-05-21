<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Gallery\ArtGallery;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_art_gallery")
 */
class ThemeArtGallery
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(nullable=false, name="theme_id", referencedColumnName="id")
     * @var Theme
     */
    private $theme;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Gallery\ArtGallery")
     * @ORM\JoinColumn(nullable=false, name="art_gallery_id", referencedColumnName="id")
     * @var ArtGallery
     */
    private $artGallery;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $name;

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
     * @return ArtGallery
     */
    public function getArtGallery(): ArtGallery
    {
        return $this->artGallery;
    }

    /**
     * @param ArtGallery $artGallery
     */
    public function setArtGallery(ArtGallery $artGallery): void
    {
        $this->artGallery = $artGallery;
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