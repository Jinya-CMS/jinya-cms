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

    public function getGallery(): SegmentPage
    {
        return $this->gallery;
    }

    public function getSegment(): Segment
    {
        return $this->segment;
    }

    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
