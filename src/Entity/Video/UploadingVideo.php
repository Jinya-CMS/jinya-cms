<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:23
 */

namespace Jinya\Entity\Video;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Video\Video")
     * @var Video
     */
    private $video;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Video\UploadingVideoChunk", mappedBy="uploadingVideo", cascade={"REMOVE"})
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

    /**
     * @return Collection
     */
    public function getChunks(): Collection
    {
        return $this->chunks;
    }

    /**
     * @param Collection $chunks
     */
    public function setChunks(Collection $chunks): void
    {
        $this->chunks = $chunks;
    }

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
}
