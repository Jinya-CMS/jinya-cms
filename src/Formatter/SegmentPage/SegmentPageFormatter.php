<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Formatter\User\UserFormatterInterface;

class SegmentPageFormatter implements SegmentPageFormatterInterface
{
    /** @var array */
    private $formatted;

    /** @var SegmentPage */
    private $segmentPage;

    /** @var SegmentFormatterInterface */
    private $segmentFormatter;

    /** @var UserFormatterInterface */
    private $userFormatter;

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
     * @param SegmentPage $segmentPage
     * @return SegmentPageFormatterInterface
     */
    public function init(SegmentPage $segmentPage): SegmentPageFormatterInterface
    {
        $this->segmentPage = $segmentPage;

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return SegmentPageFormatterInterface
     */
    public function slug(): SegmentPageFormatterInterface
    {
        $this->formatted['slug'] = $this->segmentPage->getSlug();

        return $this;
    }

    /**
     * Formats the segments
     *
     * @return SegmentPageFormatterInterface
     */
    public function segments(): SegmentPageFormatterInterface
    {
        if (!$this->segmentPage->getSegments()->isEmpty()) {
            $this->formatted['segments'] = [];
        }

        $this->segmentPage->getSegments()->forAll(function (Segment $segment) {
            $this->formatted['segments'][] = $this->segmentFormatter
                ->init($segment)
                ->video()
                ->form()
                ->html()
                ->script()
                ->target()
                ->id()
                ->action()
                ->youtubeVideo()
                ->videoGallery()
                ->artGallery()
                ->artwork()
                ->format();

            return true;
        });

        return $this;
    }

    /**
     * Formats the name
     *
     * @return SegmentPageFormatterInterface
     */
    public function name(): SegmentPageFormatterInterface
    {
        $this->formatted['name'] = $this->segmentPage->getName();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return SegmentPageFormatterInterface
     */
    public function created(): SegmentPageFormatterInterface
    {
        $this->formatted['created']['by'] = $this->userFormatter
            ->init($this->segmentPage->getCreator())
            ->profile()
            ->format();
        $this->formatted['created']['at'] = $this->segmentPage->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return SegmentPageFormatterInterface
     */
    public function updated(): SegmentPageFormatterInterface
    {
        $this->formatted['updated']['by'] = $this->userFormatter
            ->init($this->segmentPage->getUpdatedBy())
            ->profile()
            ->format();
        $this->formatted['updated']['at'] = $this->segmentPage->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return SegmentPageFormatterInterface
     */
    public function history(): SegmentPageFormatterInterface
    {
        $this->formatted['history'] = $this->segmentPage->getHistory();

        return $this;
    }
}
