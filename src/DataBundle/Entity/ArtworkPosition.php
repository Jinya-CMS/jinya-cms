<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:05
 */

namespace DataBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="artwork_position")
 * @UniqueEntity(fields={"gallery", "x", "y"})
 */
class ArtworkPosition implements JsonSerializable
{

    use BaseEntity;
    /**
     * @var Gallery
     * @ORM\ManyToOne(targetEntity="DataBundle\Entity\Gallery", inversedBy="artworks")
     */
    private $gallery;
    /**
     * @var Artwork
     * @ORM\ManyToOne(targetEntity="DataBundle\Entity\Artwork", inversedBy="positions")
     */
    private $artwork;
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $x;
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $y;
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $width;
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $height;

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'x' => $this->getX(),
            'y' => $this->getY(),
            'artwork' => $this->getArtwork(),
            'gallery' => $this->getGallery(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight()
        ];
    }

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @param float $x
     */
    public function setX(float $x)
    {
        $this->x = $x;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * @param float $y
     */
    public function setY(float $y)
    {
        $this->y = $y;
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
    public function setArtwork(Artwork $artwork)
    {
        $this->artwork = $artwork;
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
    public function setGallery(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth(float $width)
    {
        $this->width = $width;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight(float $height)
    {
        $this->height = $height;
    }
}