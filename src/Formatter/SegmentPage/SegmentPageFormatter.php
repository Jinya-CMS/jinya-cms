<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Formatter\User\UserFormatterInterface;

class SegmentPageFormatter implements SegmentPageFormatterInterface
{
    private array $formatted;

    private SegmentPage $segmentPage;

    private SegmentFormatterInterface $segmentFormatter;

    private UserFormatterInterface $userFormatter;

    /**
     * SegmentPageFormatter constructor.
     */
    public function __construct(SegmentFormatterInterface $segmentFormatter, UserFormatterInterface $userFormatter)
    {
        $this->segmentFormatter = $segmentFormatter;
        $this->userFormatter = $userFormatter;
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
     */
    public function init(SegmentPage $segmentPage): SegmentPageFormatterInterface
    {
        $this->formatted = [];
        $this->segmentPage = $segmentPage;

        return $this;
    }

    /**
     * Formats the slug
     */
    public function slug(): SegmentPageFormatterInterface
    {
        $this->formatted['slug'] = $this->segmentPage->getSlug();

        return $this;
    }

    /**
     * Formats the segments
     */
    public function segments(): SegmentPageFormatterInterface
    {
        $this->formatted['segments'] = [];

        $segments = $this->segmentPage->getSegments()->toArray();
        uasort($segments, static function (Segment $a, Segment $b) {
            return $a->getPosition() > $b->getPosition();
        });

        foreach ($segments as $segment) {
            $this->formatted['segments'][] = $this->segmentFormatter
                ->init($segment)
                ->position()
                ->html()
                ->script()
                ->target()
                ->id()
                ->action()
                ->file()
                ->gallery()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the name
     */
    public function name(): SegmentPageFormatterInterface
    {
        $this->formatted['name'] = $this->segmentPage->getName();

        return $this;
    }

    /**
     * Formats the created info
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
     */
    public function history(): SegmentPageFormatterInterface
    {
        $this->formatted['history'] = $this->segmentPage->getHistory();

        return $this;
    }
}
