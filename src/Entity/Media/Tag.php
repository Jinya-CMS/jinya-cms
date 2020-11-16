<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $tag;

    /**
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Media\File", mappedBy="tags", cascade={"persist"})
     */
    private Collection $files;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFiles(): ArrayCollection
    {
        return $this->files;
    }

    public function setFiles(ArrayCollection $files): void
    {
        $this->files = $files;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }
}
