<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 16:52
 */

namespace Jinya\Entity\Artwork;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseArtEntity;
use Jinya\Entity\Base\HistoryEnabledEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="artwork")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name", message="backend.artworks.name.not_unique")
 * @UniqueEntity("slug", message="backend.artworks.slug.not_unique")
 */
class Artwork extends HistoryEnabledEntity
{
    use BaseArtEntity;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $picture;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Artwork\ArtworkPosition", mappedBy="artwork")
     */
    private $positions;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Label\Label", inversedBy="artworks", cascade={"persist"})
     */
    private $labels;

    /**
     * Artwork constructor.
     */
    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->positions = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }

    /**
     * @param Collection $labels
     */
    public function setLabels(Collection $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * @return Collection
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    /**
     * @param Collection $positions
     */
    public function setPositions(Collection $positions): void
    {
        $this->positions = $positions;
    }

    /**
     * @return string
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'creator' => $this->getCreator(),
            'createdAt' => $this->getCreatedAt(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
            'slug' => $this->slug,
            'picture' => $this->picture,
        ];
    }
}
