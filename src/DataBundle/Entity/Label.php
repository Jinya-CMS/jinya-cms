<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 07.01.2018
 * Time: 19:54
 */

namespace DataBundle\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="label")
 */
class Label implements JsonSerializable
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $name;
    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="DataBundle\Entity\Artwork", mappedBy="labels", cascade={"persist"})
     */
    private $artworks;
    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="DataBundle\Entity\Gallery", mappedBy="labels", cascade={"persist"})
     */
    private $galleries;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Collection
     */
    public function getGalleries(): ?Collection
    {
        return $this->galleries;
    }

    /**
     * @param Collection $galleries
     */
    public function setGalleries(Collection $galleries): void
    {
        $this->galleries = $galleries;
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

    /**
     * @return Collection
     */
    public function getArtworks(): ?Collection
    {
        return $this->artworks;
    }

    /**
     * @param Collection $artworks
     */
    public function setArtworks(Collection $artworks): void
    {
        $this->artworks = $artworks;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'artworks' => $this->artworks
        ];
    }
}