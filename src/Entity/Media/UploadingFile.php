<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:23
 */

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="uploading_file")
 */
class UploadingFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @var string
     */
    private ?string $id;

    /**
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Media\File")
     */
    private File $file;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Jinya\Entity\Media\UploadingFileChunk",
     *     mappedBy="uploadingFile",
     *     cascade={"REMOVE"}
     * )
     * @var Collection
     */
    private $chunks;

    /**
     * UploadingVideo constructor.
     */
    public function __construct()
    {
        $this->chunks = new ArrayCollection();
    }

    public function getChunks(): Collection
    {
        return $this->chunks;
    }

    public function setChunks(Collection $chunks): void
    {
        $this->chunks = $chunks;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
