<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.06.18
 * Time: 16:48
 */

namespace Jinya\Formatter\Video;

use Jinya\Entity\Video\Video;
use Jinya\Formatter\User\UserFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoFormatter implements VideoFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var Video */
    private $video;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var VideoPositionFormatterInterface */
    private $videoPositionFormatter;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * VideoFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UserFormatterInterface $userFormatter, UrlGeneratorInterface $urlGenerator)
    {
        $this->userFormatter = $userFormatter;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param VideoPositionFormatterInterface $videoPositionFormatter
     */
    public function setVideoPositionFormatter(VideoPositionFormatterInterface $videoPositionFormatter): void
    {
        $this->videoPositionFormatter = $videoPositionFormatter;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatting
     *
     * @param Video $Video
     * @return VideoFormatterInterface
     */
    public function init(Video $Video): VideoFormatterInterface
    {
        $this->video = $Video;

        return $this;
    }

    /**
     * Formats the video key
     *
     * @return VideoFormatterInterface
     */
    public function video(): VideoFormatterInterface
    {
        $this->formattedData['video'] = $this->urlGenerator->generate('api_video_get_video', [
            'slug' => $this->video->getSlug(),
        ]);

        return $this;
    }

    /**
     * Formats the name
     *
     * @return VideoFormatterInterface
     */
    public function name(): VideoFormatterInterface
    {
        $this->formattedData['name'] = $this->video->getName();

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return VideoFormatterInterface
     */
    public function slug(): VideoFormatterInterface
    {
        $this->formattedData['slug'] = $this->video->getSlug();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return VideoFormatterInterface
     */
    public function description(): VideoFormatterInterface
    {
        $this->formattedData['description'] = $this->video->getDescription();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return VideoFormatterInterface
     */
    public function created(): VideoFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->video->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->video->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return VideoFormatterInterface
     */
    public function updated(): VideoFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->video->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->video->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return VideoFormatterInterface
     */
    public function history(): VideoFormatterInterface
    {
        $this->formattedData['history'] = $this->video->getHistory();

        return $this;
    }

    /**
     * Formats the poster
     *
     * @return VideoFormatterInterface
     */
    public function poster(): VideoFormatterInterface
    {
        $this->formattedData['poster'] = $this->urlGenerator->generate('api_video_get_poster', [
            'slug' => $this->video->getSlug(),
        ]);

        return $this;
    }

    /**
     * Formats the galleries
     *
     * @return VideoFormatterInterface
     */
    public function galleries(): VideoFormatterInterface
    {
        $this->formattedData['galleries'] = [];

        foreach ($this->video->getPositions() as $position) {
            $this->formattedData['galleries'][] = $this->videoPositionFormatter
                ->init($position)
                ->position()
                ->id()
                ->gallery()
                ->format();
        }

        return $this;
    }
}
