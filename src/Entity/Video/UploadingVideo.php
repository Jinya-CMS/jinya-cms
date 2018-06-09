<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:23
 */

namespace Jinya\Entity\Video;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="uploading_video")
 */
class UploadingVideo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Video\Video", inversedBy="id")
     * @var Video
     */
    private $video;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $chunkPath;

    /**
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }

    /**
     * @param Video $video
     */
    public function setVideo(Video $video): void
    {
        $this->video = $video;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
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
}
