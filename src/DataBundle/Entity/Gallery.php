<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 07.11.2017
 * Time: 17:33
 */

namespace DataBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name", message="backend.galleries.name.not_unique")
 * @UniqueEntity("slug", message="backend.galleries.slug.not_unique")
 */
class Gallery extends HistoryEnabledEntity implements ArtEntityInterface
{
    use BaseArtEntity;

    public const VERTICAL = 'vertical';
    public const HORIZONTAL = 'horizontal';

    /**
     * @var ArtworkPosition[]
     * @ORM\OneToMany(targetEntity="DataBundle\Entity\ArtworkPosition", mappedBy="gallery")
     */
    private $artworks;
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $background;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $orientation = Gallery::HORIZONTAL;
    /**
     * @var UploadedFile
     */
    private $backgroundResource;

    /**
     * @return string
     */
    public function getOrientation(): string
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
     * @return ArtworkPosition[]
     */
    public function getArtworks(): array
    {
        return $this->artworks;
    }

    /**
     * @param ArtworkPosition[] $artworks
     */
    public function setArtworks(array $artworks)
    {
        $this->artworks = $artworks;
    }

    /**
     * @return UploadedFile
     */
    public function getBackgroundResource():?UploadedFile
    {
        return $this->backgroundResource;
    }

    /**
     * @param UploadedFile $backgroundResource
     */
    public function setBackgroundResource(UploadedFile $backgroundResource)
    {
        $this->backgroundResource = $backgroundResource;
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
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'history' => $this->getHistory(),
            'background' => $this->background,
            'creator' => $this->getCreator(),
            'createdAt' => $this->getCreatedAt(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
            'slug' => $this->slug
        ];
    }
}