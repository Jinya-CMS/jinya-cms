<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Gallery\VideoGallery;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_video_gallery")
 */
class ThemeVideoGallery
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
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Gallery\VideoGallery")
     * @ORM\JoinColumn(nullable=false, name="video_gallery_id", referencedColumnName="id")
     * @var VideoGallery
     */
    private $videoGallery;

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
     * @return VideoGallery
     */
    public function getVideoGallery(): VideoGallery
    {
        return $this->videoGallery;
    }

    /**
     * @param VideoGallery $videoGallery
     */
    public function setVideoGallery(VideoGallery $videoGallery): void
    {
        $this->videoGallery = $videoGallery;
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
