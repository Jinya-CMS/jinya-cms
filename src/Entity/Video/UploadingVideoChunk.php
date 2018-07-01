<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 09.06.18
 * Time: 15:05
 */

namespace Jinya\Entity\Video;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="uploading_video_chunk")
 */
class UploadingVideoChunk
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
     * @ORM\Column(type="string")
     */
    private $chunkPath;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $chunkPosition;

    /**
     * @var UploadingVideo
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Video\UploadingVideo", inversedBy="chunks")
     */
    private $uploadingVideo;

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
     * @return string
     */
    public function getChunkPath(): string
    {
        return $this->chunkPath;
    }

    /**
     * @param string $chunkPath
     */
    public function setChunkPath(string $chunkPath): void
    {
        $this->chunkPath = $chunkPath;
    }

    /**
     * @return int
     */
    public function getChunkPosition(): int
    {
        return $this->chunkPosition;
    }

    /**
     * @param int $chunkPosition
     */
    public function setChunkPosition(int $chunkPosition): void
    {
        $this->chunkPosition = $chunkPosition;
    }

    /**
     * @return UploadingVideo
     */
    public function getUploadingVideo(): UploadingVideo
    {
        return $this->uploadingVideo;
    }

    /**
     * @param UploadingVideo $uploadingVideo
     */
    public function setUploadingVideo(UploadingVideo $uploadingVideo): void
    {
        $this->uploadingVideo = $uploadingVideo;
    }
}
