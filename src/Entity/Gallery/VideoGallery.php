<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.06.18
 * Time: 18:44
 */

namespace Jinya\Entity\Gallery;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseArtEntity;
use Jinya\Entity\Base\HistoryEnabledEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="video_gallery")
 */
class VideoGallery extends HistoryEnabledEntity implements GalleryInterface
{
    use BaseArtEntity;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Video\VideoPosition", mappedBy="gallery", cascade={"persist"})
     */
    private $videos;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $background;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $orientation = self::HORIZONTAL;

    /**
     * Gallery constructor.
     */
    public function __construct()
    {
        $this->videos = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     */
    public function setOrientation(string $orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * @return Collection
     */
    public function getVideos(): ?Collection
    {
        return $this->videos;
    }

    /**
     * @param Collection $videos
     */
    public function setVideos(Collection $videos)
    {
        $this->videos = $videos;
    }

    /**
     * @return string
     */
    public function getBackground(): ?string
    {
        return $this->background;
    }

    /**
     * @param string $background
     */
    public function setBackground(?string $background)
    {
        $this->background = $background;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'background' => $this->background,
            'creator' => $this->getCreator(),
            'createdAt' => $this->getCreatedAt(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
            'slug' => $this->slug,
        ];
    }
}
