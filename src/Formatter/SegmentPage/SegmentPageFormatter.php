<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Formatter\User\UserFormatterInterface;

class SegmentPageFormatter implements SegmentPageFormatterInterface
{
    /** @var array */
    private array $formatted;

    /** @var SegmentPage */
    private SegmentPage $segmentPage;

    /** @var SegmentFormatterInterface */
    private SegmentFormatterInterface $segmentFormatter;

    /** @var UserFormatterInterface */
    private UserFormatterInterface $userFormatter;

    /**
     * SegmentPageFormatter constructor.
     * @param SegmentFormatterInterface $segmentFormatter
     * @param UserFormatterInterface $userFormatter
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
     *
     * @param SegmentPage $segmentPage
     * @return SegmentPageFormatterInterface
     */
    public function init(SegmentPage $segmentPage): SegmentPageFormatterInterface
    {
        $this->formatted = [];
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
        $this->formatted['segments'] = [];

        $segments = $this->segmentPage->getSegments()->toArray();
        uasort($segments, static function (Segment $a, Segment $b) {
            return $a->getPosition() > $b->getPosition();
        });

        foreach ($segments as $segment) {
            $this->formatted['segments'][] = $this->segmentFormatter
                ->init($segment)
                ->position()
                ->form()
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
