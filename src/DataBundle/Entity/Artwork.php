<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 16:52
 */

namespace DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="artwork")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name", message="backend.artworks.name.not_unique")
 * @UniqueEntity("slug", message="backend.artworks.slug.not_unique")
 */
class Artwork extends HistoryEnabledEntity implements ArtEntityInterface
{
    use BaseArtEntity;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $picture;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="DataBundle\Entity\ArtworkPosition", mappedBy="artwork")
     */
    private $positions;
    /**
     * @var resource|UploadedFile
     */
    private $pictureResource;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="DataBundle\Entity\Label", inversedBy="artworks", cascade={"persist"})
     */
    private $labels;
    /**
     * @var array
     */
    private $labelsChoice;

    /**
     * Artwork constructor.
     */
    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->positions = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getLabelsChoice(): ?array
    {
        return $this->labelsChoice;
    }

    /**
     * @param array $labelsChoice
     */
    public function setLabelsChoice(array $labelsChoice): void
    {
        $this->labelsChoice = $labelsChoice;
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
    public function setPositions(Collection $positions)
    {
        $this->positions = $positions;
    }

    /**
     * @return string
     */
    public function getPicture(): string
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture(string $picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return resource|UploadedFile
     */
    public function getPictureResource()
    {
        return $this->pictureResource;
    }

    /**
     * @param resource|UploadedFile $pictureResource
     */
    public function setPictureResource($pictureResource)
    {
        $this->pictureResource = $pictureResource;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'history' => $this->getHistory(),
            'creator' => $this->getCreator(),
            'createdAt' => $this->getCreatedAt(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
            'slug' => $this->slug,
            'picture' => $this->picture
        ];
    }
}