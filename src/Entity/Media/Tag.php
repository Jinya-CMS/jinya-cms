<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $tag;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Media\File", inversedBy="tags")
     */
    private $files;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Media\Folder", inversedBy="tags")
     */
    private $folders;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->folders = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return ArrayCollection
     */
    public function getFolders(): ArrayCollection
    {
        return $this->folders;
    }

    /**
     * @param ArrayCollection $folders
     */
    public function setFolders(ArrayCollection $folders): void
    {
        $this->folders = $folders;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles(): ArrayCollection
    {
        return $this->files;
    }

    /**
     * @param ArrayCollection $files
     */
    public function setFiles(ArrayCollection $files): void
    {
        $this->files = $files;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }
}
