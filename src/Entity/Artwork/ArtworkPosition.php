<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:05
 */

namespace Jinya\Entity\Artwork;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;
use Jinya\Entity\Gallery\ArtGallery;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="artwork_position")
 * @UniqueEntity(fields={"gallery", "position"})
 */
class ArtworkPosition implements JsonSerializable
{
    use BaseEntity;

    /**
     * @var ArtGallery
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Gallery\ArtGallery", inversedBy="artworks")
     */
    private $gallery;

    /**
     * @var Artwork
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Artwork\Artwork", inversedBy="positions", cascade={"persist"})
     */
    private $artwork;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'artwork' => $this->getArtwork(),
            'gallery' => $this->getGallery(),
            'position' => $this->getPosition(),
        ];
    }

    /**
     * @return Artwork
     */
    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    /**
     * @param Artwork $artwork
     */
    public function setArtwork(Artwork $artwork): void
    {
        $this->artwork = $artwork;
    }

    /**
     * @return ArtGallery
     */
    public function getGallery(): ArtGallery
    {
        return $this->gallery;
    }

    /**
     * @param ArtGallery $gallery
     */
    public function setGallery(ArtGallery $gallery): void
    {
        $this->gallery = $gallery;
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
