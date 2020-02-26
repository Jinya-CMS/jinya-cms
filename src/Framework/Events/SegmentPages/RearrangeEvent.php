<?php

namespace Jinya\Framework\Events\SegmentPages;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Framework\Events\Common\CancellableEvent;

class RearrangeEvent extends CancellableEvent
{
    public const PRE_REARRANGE = 'SegmentPreRearrange';

    public const POST_REARRANGE = 'SegmentPostRearrange';

    /** @var SegmentPage */
    private SegmentPage $gallery;

    /** @var Segment */
    private Segment $segment;

    /** @var int */
    private int $oldPosition;

    /** @var int */
    private int $newPosition;

    /**
     * RearrangeEvent constructor.
     * @param SegmentPage $gallery
     * @param Segment $segment
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(
        SegmentPage $gallery,
        Segment $segment,
        int $oldPosition,
        int $newPosition
    ) {
        $this->gallery = $gallery;
        $this->segment = $segment;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * @return SegmentPage
     */
    public function getGallery(): SegmentPage
    {
        return $this->gallery;
    }

    /**
     * @return Segment
     */
    public function getSegment(): Segment
    {
        return $this->segment;
    }

    /**
     * @return int
     */
    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    /**
     * @return int
     */
    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
