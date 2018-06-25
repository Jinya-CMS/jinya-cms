<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.06.18
 * Time: 18:45
 */

namespace Jinya\Entity\Galleries;


use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\BaseEntity;
use Jinya\Entity\Video\Video;
use Jinya\Entity\Video\YoutubeVideo;

/**
 * @ORM\Entity
 * @ORM\Table(name="video_position")
 */
class VideoPosition
{
    use BaseEntity;

    /**
     * @var VideoGallery
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Galleries\VideoGallery", inversedBy="artworks")
     */
    private $gallery;

    /**
     * @var Video
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Video\Video", inversedBy="positions", cascade={"persist"})
     */
    private $video;

    /**
     * @var YoutubeVideo
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Video\YoutubeVideo", inversedBy="positions", cascade={"persist"})
     */
    private $youtubeVideo;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @return VideoGallery
     */
    public function getGallery(): VideoGallery
    {
        return $this->gallery;
    }

    /**
     * @param VideoGallery $gallery
     */
    public function setGallery(VideoGallery $gallery): void
    {
        $this->gallery = $gallery;
    }

    /**
     * @return YoutubeVideo
     */
    public function getYoutubeVideo(): ?YoutubeVideo
    {
        return $this->youtubeVideo;
    }

    /**
     * @param YoutubeVideo $youtubeVideo
     */
    public function setYoutubeVideo(YoutubeVideo $youtubeVideo): void
    {
        $this->youtubeVideo = $youtubeVideo;
    }

    /**
     * @return Video
     */
    public function getVideo(): ?Video
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
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
