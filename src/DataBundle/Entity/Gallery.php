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
class Gallery extends HistoryEnabledEntity
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
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $background;
    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $slug;
    /**
     * @var UploadedFile
     */
    private $backgroundResource;

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
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

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
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
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
            'updatedBy' => $this->getUpdatedBy()
        ];
    }
}