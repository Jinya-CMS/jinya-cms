<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 09.06.18
 * Time: 15:05
 */

namespace Jinya\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="uploading_file_chunk")
 */
class UploadingFileChunk
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $id = -1;

    /**
     * @ORM\Column(type="string")
     */
    private string $chunkPath;

    /**
     * @ORM\Column(type="integer")
     */
    private int $chunkPosition;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\UploadingFile", inversedBy="chunks")
     */
    private UploadingFile $uploadingFile;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getChunkPath(): string
    {
        return $this->chunkPath;
    }

    public function setChunkPath(string $chunkPath): void
    {
        $this->chunkPath = $chunkPath;
    }

    public function getChunkPosition(): int
    {
        return $this->chunkPosition;
    }

    public function setChunkPosition(int $chunkPosition): void
    {
        $this->chunkPosition = $chunkPosition;
    }

    public function getUploadingFile(): UploadingFile
    {
        return $this->uploadingFile;
    }

    public function setUploadingFile(UploadingFile $uploadingFile): void
    {
        $this->uploadingFile = $uploadingFile;
    }
}
