<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:20
 */

namespace Jinya\Formatter\Video;

use Jinya\Entity\Video\VideoPosition;
use Jinya\Formatter\FormatterInterface;
use Jinya\Formatter\Gallery\VideoGalleryFormatterInterface;

class VideoPositionFormatter implements VideoPositionFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var VideoPosition */
    private $videoPosition;

    /** @var VideoGalleryFormatterInterface */
    private $galleryFormatterInterface;

    /** @var VideoFormatterInterface */
    private $videoFormatterInterface;

    /** @var YoutubeVideoFormatterInterface */
    private $youtubeVideoFormatterInterface;

    /**
     * @param YoutubeVideoFormatterInterface $youtubeVideoFormatterInterface
     */
    public function setYoutubeVideoFormatterInterface(YoutubeVideoFormatterInterface $youtubeVideoFormatterInterface
    ): void
    {
        $this->youtubeVideoFormatterInterface = $youtubeVideoFormatterInterface;
    }

    /**
     * @param VideoGalleryFormatterInterface $galleryFormatterInterface
     */
    public function setGalleryFormatterInterface(VideoGalleryFormatterInterface $galleryFormatterInterface): void
    {
        $this->galleryFormatterInterface = $galleryFormatterInterface;
    }

    /**
     * @param VideoFormatterInterface $videoFormatterInterface
     */
    public function setVideoFormatterInterface(VideoFormatterInterface $videoFormatterInterface): void
    {
        $this->videoFormatterInterface = $videoFormatterInterface;
    }

    /**
     * Initializes the format
     *
     * @param VideoPosition $videoPosition
     * @return VideoPositionFormatterInterface
     */
    public function init(VideoPosition $videoPosition): VideoPositionFormatterInterface
    {
        $this->formattedData = [];
        $this->videoPosition = $videoPosition;

        return $this;
    }

    /**
     * Formats the video
     *
     * @return VideoPositionFormatterInterface
     */
    public function video(): VideoPositionFormatterInterface
    {
        $this->formattedData['video'] = $this->videoFormatterInterface
            ->init($this->videoPosition->getVideo())
            ->slug()
            ->name()
            ->poster()
            ->video()
            ->description()
            ->format();

        return $this;
    }

    /**
     * Formats the gallery
     *
     * @return VideoPositionFormatterInterface
     */
    public function gallery(): VideoPositionFormatterInterface
    {
        $this->formattedData['gallery'] = $this->galleryFormatterInterface
            ->init($this->videoPosition->getGallery())
            ->slug()
            ->name()
            ->description()
            ->format();

        return $this;
    }

    /**
     * Formats the position
     *
     * @return VideoPositionFormatterInterface
     */
    public function position(): VideoPositionFormatterInterface
    {
        $this->formattedData['id'] = $this->videoPosition->getPosition();

        return $this;
    }

    /**
     * Formats the id
     *
     * @return VideoPositionFormatterInterface
     */
    public function id(): VideoPositionFormatterInterface
    {
        $this->formattedData['id'] = $this->videoPosition->getId();

        return $this;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Formats the youtube video
     *
     * @return VideoPositionFormatterInterface
     */
    public function youtubeVideo(): VideoPositionFormatterInterface
    {
        $this->formattedData['video'] = $this->youtubeVideoFormatterInterface
            ->init($this->videoPosition->getYoutubeVideo())
            ->slug()
            ->name()
            ->videoKey()
            ->description()
            ->format();

        return $this;
    }
}
