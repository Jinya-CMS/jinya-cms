<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Formatter\Artwork\ArtworkFormatterInterface;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Formatter\Gallery\ArtGalleryFormatterInterface;
use Jinya\Formatter\Gallery\VideoGalleryFormatterInterface;
use Jinya\Formatter\Video\VideoFormatterInterface;
use Jinya\Formatter\Video\YoutubeVideoFormatterInterface;

class SegmentFormatter implements SegmentFormatterInterface
{
    /** @var array */
    private $formatted;

    /** @var Segment */
    private $segment;

    /** @var ArtworkFormatterInterface */
    private $artworkFormatter;

    /** @var VideoFormatterInterface */
    private $videoFormatter;

    /** @var YoutubeVideoFormatterInterface */
    private $youtubeVideoFormatter;

    /** @var VideoGalleryFormatterInterface */
    private $videoGalleryFormatter;

    /** @var ArtGalleryFormatterInterface */
    private $artGalleryFormatter;

    /** @var FormFormatterInterface */
    private $formFormatter;

    /**
     * SegmentFormatter constructor.
     * @param ArtworkFormatterInterface $artworkFormatter
     * @param VideoFormatterInterface $videoFormatter
     * @param YoutubeVideoFormatterInterface $youtubeVideoFormatter
     * @param VideoGalleryFormatterInterface $videoGalleryFormatter
     * @param ArtGalleryFormatterInterface $artGalleryFormatter
     * @param FormFormatterInterface $formFormatter
     */
    public function __construct(
        ArtworkFormatterInterface $artworkFormatter,
        VideoFormatterInterface $videoFormatter,
        YoutubeVideoFormatterInterface $youtubeVideoFormatter,
        VideoGalleryFormatterInterface $videoGalleryFormatter,
        ArtGalleryFormatterInterface $artGalleryFormatter,
        FormFormatterInterface $formFormatter
    ) {
        $this->artworkFormatter = $artworkFormatter;
        $this->videoFormatter = $videoFormatter;
        $this->youtubeVideoFormatter = $youtubeVideoFormatter;
        $this->videoGalleryFormatter = $videoGalleryFormatter;
        $this->artGalleryFormatter = $artGalleryFormatter;
        $this->formFormatter = $formFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formatted;
    }

    /**
     * Initializes the formatting
     *
     * @param Segment $segment
     * @return SegmentFormatterInterface
     */
    public function init(Segment $segment): SegmentFormatterInterface
    {
        $this->formatted = [];
        $this->segment = $segment;

        return $this;
    }

    /**
     * Formats the id
     *
     * @return SegmentFormatterInterface
     */
    public function id(): SegmentFormatterInterface
    {
        $this->formatted['id'] = $this->segment->getId();

        return $this;
    }

    /**
     * Formats the action
     *
     * @return SegmentFormatterInterface
     */
    public function action(): SegmentFormatterInterface
    {
        if ($this->segment->getArtwork()) {
            $this->formatted['action'] = $this->segment->getAction();
        }

        return $this;
    }

    /**
     * Formats the target
     *
     * @return SegmentFormatterInterface
     */
    public function target(): SegmentFormatterInterface
    {
        if ($this->segment->getArtwork()) {
            $this->formatted['target'] = $this->segment->getTarget();
        }

        return $this;
    }

    /**
     * Formats the script
     *
     * @return SegmentFormatterInterface
     */
    public function script(): SegmentFormatterInterface
    {
        if ($this->segment->getArtwork()) {
            $this->formatted['script'] = $this->segment->getScript();
        }

        return $this;
    }

    /**
     * Formats the artwork
     *
     * @return SegmentFormatterInterface
     */
    public function artwork(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getArtwork()) {
            $this->formatted['artwork'] = $this
                ->artworkFormatter
                ->init($this->segment->getArtwork())
                ->slug()
                ->name()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the video
     *
     * @return SegmentFormatterInterface
     */
    public function video(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getVideo()) {
            $this->formatted['video'] = $this
                ->videoFormatter
                ->init($this->segment->getVideo())
                ->slug()
                ->name()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the youtube video
     *
     * @return SegmentFormatterInterface
     */
    public function youtubeVideo(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getYoutubeVideo()) {
            $this->formatted['youtubeVideo'] = $this
                ->youtubeVideoFormatter
                ->init($this->segment->getYoutubeVideo())
                ->slug()
                ->name()
                ->videoKey()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the video gallery
     *
     * @return SegmentFormatterInterface
     */
    public function videoGallery(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getVideoGallery()) {
            $this->formatted['videoGallery'] = $this
                ->videoGalleryFormatter
                ->init($this->segment->getVideoGallery())
                ->slug()
                ->name()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the art gallery
     *
     * @return SegmentFormatterInterface
     */
    public function artGallery(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getArtGallery()) {
            $this->formatted['artGallery'] = $this
                ->artGalleryFormatter
                ->init($this->segment->getArtGallery())
                ->slug()
                ->name()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the form
     *
     * @return SegmentFormatterInterface
     */
    public function form(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getForm()) {
            $this->formatted['form'] = $this
                ->formFormatter
                ->init($this->segment->getForm())
                ->slug()
                ->name()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the html
     *
     * @return SegmentFormatterInterface
     */
    public function html(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getHtml()) {
            $this->formatted['html'] = $this->segment->getHtml();
        }

        return $this;
    }

    /**
     * Formats the position
     *
     * @return SegmentFormatterInterface
     */
    public function position(): SegmentFormatterInterface
    {
        $this->formatted['position'] = $this->segment->getPosition();

        return $this;
    }
}
