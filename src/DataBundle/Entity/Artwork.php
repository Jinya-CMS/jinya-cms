<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 16:52
 */

namespace DataBundle\Entity;

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
     * @var ArtworkPosition[]
     * @ORM\OneToMany(targetEntity="DataBundle\Entity\ArtworkPosition", mappedBy="artwork")
     */
    private $positions;
    /**
     * @var resource|UploadedFile
     */
    private $pictureResource;

    /**
     * @return ArtworkPosition[]
     */
    public function getPositions(): array
    {
        return $this->positions;
    }

    /**
     * @param ArtworkPosition[] $positions
     */
    public function setPositions(array $positions)
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